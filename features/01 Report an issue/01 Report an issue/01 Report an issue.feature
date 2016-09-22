Feature: Report an issue

    As a customer
    I want to report an issue
    so that someone gains knowledge of my problem or idea.

    Scenario: A bug is reported
        When I report a "bug" to the feedback forum with title "No issue reporting possible" and text:
            """
            I cannot report issues – 500er Server error :-(
            """
        Then the issue should have been reported to the feedback forum as "bug"
        And I should be the reporter of the issue

    Scenario: An enhancement is reported
        When I report an "enhancement" to the feedback forum with title "Issue reporting" and text:
            """
            It would be nice if we could report issues :-)
            """
        Then the issue should have been reported to the feedback forum as "enhancement"
        And I should be the reporter of the issue
