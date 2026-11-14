<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Backend;

use Contao\Input;
use Contao\InputEncodingMode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Query and form values of the current back-end request, encoded as Contao's
 * Input::get() and Input::post() return them (Input::encodeInputRecursive()),
 * without those methods: Contao 6 deprecates them (removed in Contao 7) and
 * points to the request object. Contao 5.3 to 6.0 offer the same request and
 * encoding. Only for keys no other code sets through Input::setGet() (Contao
 * itself does so for pid, mode and lp in the back end).
 */
final class RequestInput
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /** A query parameter as Input::get() returns it; null when missing. */
    public function query(string $key): array|string|null
    {
        $value = $this->request()?->query->all()[$key] ?? null;

        return null === $value ? null : Input::encodeInputRecursive($value, InputEncodingMode::encodeAll);
    }

    /** A form value as Input::post() returns it in the back end; null when missing. */
    public function post(string $key): array|string|null
    {
        $value = $this->request()?->request->all()[$key] ?? null;

        return null === $value ? null : Input::encodeInputRecursive($value, InputEncodingMode::encodeAll, false);
    }

    /**
     * Puts a value into the submitted form, where Contao's form handling reads
     * it for the fields that follow (instead of Input::setPost()).
     */
    public function setPost(string $key, string $value): void
    {
        $this->request()?->request->set($key, $value);
    }

    private function request(): Request|null
    {
        return $this->requestStack->getCurrentRequest();
    }
}
