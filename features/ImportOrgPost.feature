Feature: Import Post from Org file
  In order to publish content written in Emacs
  As a blog author
  I want to import .org files as posts

  Scenario: Import valid org file with all metadata
    Given I am an API client
    And the database is empty
    And an org file exists at "/tmp/test-post.org" with content:
      """
      #+TITLE: Mi primer post desde Emacs
      #+AUTHOR: Pascual Muñoz Galián
      #+DATE: 2024-01-15

      Este es el contenido del artículo.

      * Sección 1
      Más contenido aquí.
      """
    And the payload is:
      """
      {
        "filePath": "/tmp/test-post.org"
      }
      """
    When i send payload to endpoint "/post/import-org" with method "POST"
    Then the response code should be 202
    And a post exists with headline "Mi primer post desde Emacs"

  Scenario: Import org file without author uses default
    Given I am an API client
    And the database is empty
    And an org file exists at "/tmp/no-author.org" with content:
      """
      #+TITLE: Post sin autor explícito
      #+DATE: 2024-02-20

      Contenido del post sin autor.
      """
    And the payload is:
      """
      {
        "filePath": "/tmp/no-author.org"
      }
      """
    When i send payload to endpoint "/post/import-org" with method "POST"
    Then the response code should be 202
    And a post exists with author "Pascual Muñoz Galián"

  Scenario: Import org file without title fails
    Given I am an API client
    And an org file exists at "/tmp/no-title.org" with content:
      """
      #+AUTHOR: Someone

      Content without title.
      """
    And the payload is:
      """
      {
        "filePath": "/tmp/no-title.org"
      }
      """
    When i send payload to endpoint "/post/import-org" with method "POST"
    Then the response code should be 400

  Scenario: Import non-existent file fails
    Given I am an API client
    And the payload is:
      """
      {
        "filePath": "/tmp/non-existent-file.org"
      }
      """
    When i send payload to endpoint "/post/import-org" with method "POST"
    Then the response code should be 400

  Scenario: Blog displays imported posts
    Given I am an API client
    And the database is empty
    And an org file exists at "/tmp/blog-post.org" with content:
      """
      #+TITLE: PHP Asíncrono en Acción
      #+AUTHOR: Pascual Muñoz Galián
      #+DATE: 2024-03-01

      En el mundo del desarrollo web, la eficiencia es crucial.
      """
    And the payload is:
      """
      {
        "filePath": "/tmp/blog-post.org"
      }
      """
    When i send payload to endpoint "/post/import-org" with method "POST"
    Then the response code should be 202
    When I request "/post" with method "GET"
    Then the response code should be 200
    And The response Items are Posts
