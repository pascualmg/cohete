#!/usr/bin/env nix-shell
#!nix-shell -i runghc -p "haskellPackages.ghcWithPackages (ps: with ps; [ directory filepath text bytestring aeson http-conduit http-types ])"
{-# LANGUAGE OverloadedStrings #-}
{-# LANGUAGE RecordWildCards #-}

import Control.Monad (forM, when)
import Data.Aeson (object, (.=), encode)
import Data.List (isPrefixOf)
import Data.Text (Text)
import Network.HTTP.Simple
import System.Directory (doesFileExist, listDirectory, getCurrentDirectory)
import System.FilePath ((</>))
import System.IO (hFlush, stdout)
import qualified Data.ByteString.Lazy.Char8 as BL
import qualified Data.Text as T
import qualified Data.Text.IO as TIO

ollamaApiEndpoint :: String
ollamaApiEndpoint = "http://localhost:11434/api/generate"

maxTokens :: Int
maxTokens = 128000

warningThreshold :: Double
warningThreshold = 0.9

data FileInfo = FileInfo
    { fiPath :: FilePath
    , fiContent :: Text
    , fiSize :: Int
    } deriving (Show)

scanDirectory :: FilePath -> IO [FileInfo]
scanDirectory dir = do
    entries <- listDirectory dir
    files <- forM entries $ \entry -> do
        let path = dir </> entry
        isFile <- doesFileExist path
        if isFile && not ("." `isPrefixOf` entry)
            then do
                content <- TIO.readFile path
                return [FileInfo path content (T.length content)]
            else return []
    return $ concat files
  
prepareContext :: [FileInfo] -> (Text, Int, Int, Int)
prepareContext files =
    let (context, totalTokens, filesIncluded, _) = 
            foldl accumulate ("", 0, 0, maxTokens) files
    in (context, totalTokens, filesIncluded, length files)
  where
    accumulate (ctx, tokens, included, remaining) FileInfo{..} =
        let fileContent = T.unlines ["File: " <> T.pack fiPath, "", fiContent, ""]
            tokenEstimate = T.length fileContent `div` 4
        in if tokenEstimate > remaining
           then (ctx, tokens, included, remaining)
           else (ctx <> fileContent, tokens + tokenEstimate, included + 1, remaining - tokenEstimate)


queryOllama :: Text -> Text -> IO (Maybe Text)
queryOllama context instruction = do
    let promptText = "Project Context:\n" <> context <> "\n\nInstruction: " <> instruction
        requestObject = object
            [ "model" .= ("llama3.1" :: Text)  -- AQUI SE AJUSTA EL MODELO 
            , "prompt" .= promptText
            ]
    
    request <- parseRequest ollamaApiEndpoint
    let request' = setRequestMethod "POST"
                 $ setRequestHeader "Content-Type" ["application/json"]
                 $ setRequestBodyLBS (encode requestObject) request

    response <- httpLBS request'
    let responseBody = getResponseBody response
    case BL.unpack responseBody of
        '{':_ -> case lookup "response" $ BL.unpack responseBody of
            Just content -> return $ Just $ T.pack content
            Nothing -> return Nothing
        _ -> return $ Just $ T.pack $ BL.unpack responseBody

main :: IO ()
main = do
    currentDir <- getCurrentDirectory
    putStrLn $ "Escaneando el directorio: " ++ currentDir
    files <- scanDirectory currentDir
    
    let (context, totalTokens, filesIncluded, totalFiles) = prepareContext files
        tokenUsagePercentage = (fromIntegral totalTokens / fromIntegral maxTokens) * 100

    putStrLn $ "Uso de tokens: " ++ show totalTokens ++ " de " ++ show maxTokens ++
               " (" ++ show (round tokenUsagePercentage :: Int) ++ "%)"
    putStrLn $ "Archivos incluidos en el contexto: " ++ show filesIncluded ++ " de " ++ show totalFiles

    when (totalTokens >= round (fromIntegral maxTokens * warningThreshold)) $
        putStrLn $ "Advertencia: Se ha alcanzado el " ++ show (round $ warningThreshold * 100) ++ "% del límite de tokens."

    when (filesIncluded < totalFiles) $
        putStrLn "Advertencia: No se pudieron incluir todos los archivos debido al límite de tokens."

    let loop = do
            putStr "Ingresa tu instrucción (o 'salir' para terminar): "
            hFlush stdout
            instruction <- TIO.getLine
            if T.toLower instruction == "salir"
                then putStrLn "¡Gracias por usar el asistente Ollama!"
                else do
                    response <- queryOllama context instruction
                    case response of
                        Just r -> do
                            putStrLn "\nRespuesta de Ollama:"
                            TIO.putStrLn r
                            putStrLn ""
                        Nothing -> putStrLn "No se pudo obtener una respuesta del modelo."
                    loop

    loop
