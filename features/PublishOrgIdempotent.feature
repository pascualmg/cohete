Feature: Publishing an org post is idempotent by slug
  In order to edit a published post without duplicating it
  As a blog author
  I want re-publishing the same .org (same #+SLUG) to UPDATE the post, not create a copy

  Scenario: Re-publishing the same slug updates the post instead of duplicating it
    Given I am an API client
    And the database has fixtures
    When I publish org to "/post/org" with bearer "ambrosio-cohete-2026" and body:
      """
      #+TITLE: Idempotencia v1
      #+SLUG: idempotencia-demo
      #+AUTHOR: Ambrosio

      Primera version del post.
      """
    Then the response code should be 202
    When I publish org to "/post/org" with bearer "ambrosio-cohete-2026" and body:
      """
      #+TITLE: Idempotencia v2 editado
      #+SLUG: idempotencia-demo
      #+AUTHOR: Ambrosio

      Segunda version, mismo slug, debe ACTUALIZAR.
      """
    Then the response code should be 202
    And exactly 1 post exists with slug "idempotencia-demo"
    And a post exists with headline "Idempotencia v2 editado"

  Scenario: Publishing org without a #+SLUG is rejected
    Given I am an API client
    And the database has fixtures
    When I publish org to "/post/org" with bearer "ambrosio-cohete-2026" and body:
      """
      #+TITLE: Post sin slug
      #+AUTHOR: Ambrosio

      Este org no trae SLUG, debe fallar.
      """
    Then the response code should be 400

  Scenario: Publishing org with an invalid bearer is forbidden
    Given I am an API client
    And the database has fixtures
    When I publish org to "/post/org" with bearer "token-falso-no-existe" and body:
      """
      #+TITLE: Intruso
      #+SLUG: intruso-demo
      #+AUTHOR: Ambrosio

      Token invalido.
      """
    Then the response code should be 403
