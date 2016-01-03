Feature: Git repo processing
  As a source code writer I want to be able to
  calculate my volatility metric

  Scenario: Simple git repo
    Given It is Unix
    Given I run bash:
      """
      git init .
      git config user.email test@teamed.io
      git config user.name test
      echo 'hello, world!' > test.txt
      git add test.txt
      git commit -am test
      """
    When I run bin/volatility with "-f int"
    Then Exit code is zero
    And Stdout contains "1"

  Scenario: Real git repo
    Given I run bash:
      """
      git clone https://github.com/yegor256/volatility.git volatility-repo
      """
    When I run bin/volatility with "-f int -d volatility-repo"
    Then Exit code is zero

