Feature: Reject an issue

    As a supporter
    I want to make the implementation of issues visible
    so that the customers notice the progress.

    Scenario: An issue is implemented
        Given an issue that has been reported
        When I implement the issue
        Then the issue should be implemented
        And the issue should be closed
