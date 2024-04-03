Feature: Post Endpoint
  In order to interact with the microservice
  As a client software
  I need to be able to call GET /post and receive a JSON response with the posts

  Scenario: Get post returns empty JSON
    Given I am an API client
    And the database is empty
    When I request "/post" with method "GET"
    Then the response code should be 200
    And the response should be an empty Json Array

  Scenario: Get post returns some posts
    Given I am an API client
    And the database has fixtures
    When I request "/post" with method "GET"
    Then the response code should be 200
    And The response Items are Posts