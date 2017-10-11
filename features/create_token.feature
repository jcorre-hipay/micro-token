Feature: Creating a credit card token

  In order to secure payment transactions
  As a payment service provider
  I need to be able to convert a credit card number to a token

  Scenario: Creating a token with md5 algorithm

    Given the key "l1br4ry" has been registered with identifier 4
    When I send the request "POST /token" with:
      | parameter      | value           |
      | card_number    | 372999410121001 |
      | algorithm      | md5             |
      | key_identifier | 4               |

    Then the response status code should be 201
    And the response contents should be:
      """
      {
        "code": 201,
        "token": "5d4122f7fcbbf9d3738176596160a741"
      }
      """

  Scenario: Creating a token with sha1 algorithm

    Given the key "l1br4ry" has been registered with identifier 4
    When I send the request "POST /token" with:
      | parameter      | value           |
      | card_number    | 372999410121001 |
      | algorithm      | sha1            |
      | key_identifier | 4               |

    Then the response status code should be 201
    And the response contents should be:
      """
      {
        "code": 201,
        "token": "62f6446c839e3749a938fa7d468c79f5d247c3c2"
      }
      """

  Scenario: Creating a token with sha256 algorithm

    Given the key "l1br4ry" has been registered with identifier 4
    When I send the request "POST /token" with:
      | parameter      | value           |
      | card_number    | 372999410121001 |
      | algorithm      | sha256          |
      | key_identifier | 4               |

    Then the response status code should be 201
    And the response contents should be:
      """
      {
        "code": 201,
        "token": "c061628b32afd532463daf2b771cb7306cbbfea3857bcd21f1785c7eed1efb54"
      }
      """
