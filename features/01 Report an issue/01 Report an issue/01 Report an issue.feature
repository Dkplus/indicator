Feature: Report an issue

    As a reporter
    I want to report an issue
    so that someone gains knowledge of my problem or idea.

    Scenario: An issue is reported
        When I report an issue with title "No issue reporting possible" and text:
            """
            I cannot report issues :-(
            """
        Then the issue should have been reported
        And I should be the reporter of the issue
