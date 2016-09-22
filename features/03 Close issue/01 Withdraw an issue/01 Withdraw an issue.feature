Feature: Withdraw an issue

    As a customer
    I want to withdraw issues reported by me
    so that no one cares about falsely reported issues.

    Scenario: An issue is withdrawn
        Given an issue that has been reported by myself to the feedback forum
        When I withdraw the issue
        Then the issue should be withdrawn
        And the issue should be closed
