Feature: Post Endpoint
  In order to create a new post in the microservice
  as a API client
  I need to send the post data to create one


  Scenario: Post data is send and is created
    Given I am an API client
    And the database is empty
    And the payload is:
    """
    {
     "id": "0000ce1a-b329-3459-b82f-9efb54a69ef5",
     "headline": "Sample Headline",
     "articleBody": "Sample Article Body",
     "image": "image url",
     "author": "Author Name",
     "datePublished": "2024-02-26T00:57:49+00:00"
    }
    """
    When i send payload to endpoint "/post" with method "POST"
    Then the response code should be 202
    And the post with id "0000ce1a-b329-3459-b82f-9efb54a69ef5" exists
