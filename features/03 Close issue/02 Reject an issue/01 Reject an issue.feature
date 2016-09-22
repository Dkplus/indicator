Feature: Reject an issue

    As a supporter
    I want to reject issues
    so that the feedback forum stays overseeable.

    Scenario: An issue is rejected
        Given an issue that has been reported
        When I reject the issue
        Then the issue should be rejected
        And the issue should be closed
