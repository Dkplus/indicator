Feature: Report an issue

    As a customer
    I want to report an issue
    so that someone gains knowledge of my problem or idea.

    Scenario: An issue is reported
        When I report an issue to the feedback forum with title "No issue reporting possible" and text:
            """
            I cannot report issues :-(
            """
        Then the issue should have been reported to the feedback forum
        And I should be the reporter of the issue
