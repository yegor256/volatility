Feature: Command Line Processing
  As a Git developer I want to calculate volatility of my repo

  Scenario: Help can be printed
    When I run bin/volatility with "--help"
    Then Exit code is zero
    And Stdout contains "--help"

  Scenario: Volatility can be calculated
    When I run bash with:
    """
    if git config user.email; then
      echo Git email already set
    else
      git config user.name "NoName"
      git config user.email "noname@example.com"
    fi
    mkdir tmp
    cd tmp
    git init .
    touch test.txt
    git add test.txt
    git commit -am 'test'
    """
    Then I run bin/volatility with "--verbose --home=./tmp"
    Then Exit code is zero
