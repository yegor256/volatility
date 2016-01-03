Feature: Command Line Processing
  As a source code writer I want to be able to
  calculate my volatility metric

  Scenario: Help can be printed
    When I run bin/volatility with "-h"
    Then Exit code is zero
    And Stdout contains "--format"
