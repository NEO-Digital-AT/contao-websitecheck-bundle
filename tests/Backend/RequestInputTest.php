<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Backend;

use Neo\WebsitecheckBundle\Backend\RequestInput;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

#[CoversClass(RequestInput::class)]
final class RequestInputTest extends TestCase
{
    public function testQueryValuesAreEncodedLikeInputGet(): void
    {
        $input = $this->input(new Request(['act' => 'edit', 'q' => '<b onclick="x()">{{date}}</b>', 'list' => ['a=1', '#2']]));

        $this->assertSame('edit', $input->query('act'));
        $this->assertSame('&#60;b onclick&#61;&#34;x&#40;&#41;&#34;&#62;&#123;&#123;date&#125;&#125;&#60;/b&#62;', $input->query('q'), 'no HTML, no insert tags');
        $this->assertSame(['a&#61;1', '&#35;2'], $input->query('list'));
        $this->assertNull($input->query('missing'));
    }

    public function testFormValuesAreEncodedLikeInputPostInTheBackEnd(): void
    {
        $input = $this->input(new Request([], ['title' => '<i>Chat</i> {{date}}', 'nwc_tr' => ['de' => ['title' => '"x"']]]));

        $this->assertSame('&#60;i&#62;Chat&#60;/i&#62; {{date}}', $input->post('title'), 'insert tags stay in the back end');
        $this->assertSame(['de' => ['title' => '&#34;x&#34;']], $input->post('nwc_tr'));
        $this->assertNull($input->post('missing'));
    }

    public function testAValueSetForTheFormIsReadByTheFieldsThatFollow(): void
    {
        $request = new Request();
        $input = $this->input($request);

        $input->setPost('category', 'statistics');

        $this->assertSame('statistics', $request->request->get('category'));
        $this->assertSame('statistics', $input->post('category'));
    }

    public function testWithoutARequestThereIsNothing(): void
    {
        $input = new RequestInput(new RequestStack());

        $this->assertNull($input->query('act'));
        $this->assertNull($input->post('title'));
        $input->setPost('title', 'x');
        $this->assertNull($input->post('title'));
    }

    private function input(Request $request): RequestInput
    {
        $stack = new RequestStack();
        $stack->push($request);

        return new RequestInput($stack);
    }
}
