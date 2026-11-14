<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Mail;

use Neo\WebsitecheckBundle\License\Credit;
use Neo\WebsitecheckBundle\Mail\ReportMailer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Email;

#[CoversClass(ReportMailer::class)]
final class ReportMailerTest extends TestCase
{
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    public function testEmbeddedImagesBecomeInlineAttachmentsOnce(): void
    {
        $email = new Email();
        $html = ReportMailer::inlineImages($email, '<img src="data:image/png;base64,'.self::PNG.'"><img src=\'data:image/png;base64,'.self::PNG.'\'><img src="data:image/png;base64,!!!">');

        $this->assertSame(1, preg_match_all('#src="cid:(img1-[a-f0-9]{8}\.png)"#', $html, $double));
        $this->assertSame(1, preg_match_all("#src='cid:(img1-[a-f0-9]{8}\\.png)'#", $html, $single));
        $this->assertSame($double[1], $single[1], 'the same image twice is one attachment');
        $this->assertStringContainsString('data:image/png;base64,!!!', $html, 'invalid data stays');
        $this->assertCount(1, $email->getAttachments());
        $this->assertSame('image/png', $email->getAttachments()[0]->getMediaType().'/'.$email->getAttachments()[0]->getMediaSubtype());
    }

    public function testTheTextVersionOfAnEmail(): void
    {
        $text = ReportMailer::htmlToText('<html><head><title>T</title><style>p{}</style></head><body><h1>Your check</h1><p>Grade&nbsp;2<br>See <a href="https://www.example.com/report?a=1&amp;b=2">the report</a> or <a href="mailto:office@example.com">write</a>.</p><ul><li>One</li><li>Two</li></ul><table><tr><td>A</td><td>B</td></tr></table></body></html>');

        $this->assertSame("Your check\n\nGrade 2\nSee the report (https://www.example.com/report?a=1&b=2) or write.\n\n- One\n- Two\n\nA B", $text);
    }

    public function testTheCreditLineReadsAsText(): void
    {
        $this->assertStringEndsWith('powered by NEO Digital (https://neo-digital.at)', ReportMailer::htmlToText(Credit::mail('<body><p>Report</p></body>')));
    }
}
