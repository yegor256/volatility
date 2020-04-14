Feature: Command Line Processing
  As a Git developer I want to calculate volatility of my repo

  Scenario: Help can be printed
    When I run bin/volatility with "--help"
    Then Exit code is zero
    And Stdout contains "--help"

  Scenario: Volatility can be calculated
    When I run bash with:
    """
    git config --global user.name "NoName"
    git config --global user.email "noname@example.com"
    mkdir tmp
    cd tmp
    git init .
    touch test.txt
    git add test.txt
    git commit -am 'test'
    """
    Then I run bin/volatility with "--verbose --home=./tmp"
    Then Exit code is zero
