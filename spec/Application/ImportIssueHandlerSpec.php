<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\Application;

use Dkplus\Indicator\Application\ImportIssueHandler;
use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\IssueNotFound;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ImportIssueHandler
 */
class ImportIssueHandlerSpec extends ObjectBehavior
{
    function let(FeedbackForum $feedbackForum)
    {
        $this->beConstructedWith($feedbackForum);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImportIssueHandler::class);
    }

    function it_registers_non_existing_issues(FeedbackForum $feedbackForum)
    {
        $command = ImportIssueBuilder::anImportIssueCommand()->build();

        $feedbackForum
            ->withId(IssueId::fromString($command->issueId()))
            ->willThrow(IssueNotFound::withId($command->issueId()));
        $feedbackForum->add(Argument::any())->shouldBeCalled();

        $this->getWrappedObject()->__invoke($command);

    }

    function it_imports_existing_issues_again(FeedbackForum $feedbackForum, Issue $issue)
    {
        $command = ImportIssueBuilder::anImportIssueCommand()->build();
        $feedbackForum
            ->withId(IssueId::fromString($command->issueId()))
            ->willReturn($issue);

        $this->getWrappedObject()->__invoke($command);

        $issue->importFromExternalService(Argument::cetera())->shouldHaveBeenCalled();
    }
}
