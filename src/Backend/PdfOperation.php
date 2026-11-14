<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Backend;

use Contao\CoreBundle\Exception\ResponseException;
use Contao\DataContainer;
use Neo\WebsitecheckBundle\Check\CheckRepository;
use Neo\WebsitecheckBundle\Report\PdfGenerator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Back-end row operation "key=pdf": opens the PDF report of a finished check
 * (the stored one, made when it is missing). Also without a licence: the PDF
 * is for the site operator then.
 */
final class PdfOperation
{
    public function __construct(
        private readonly CheckRepository $repository,
        private readonly PdfGenerator $pdf,
        private readonly RequestInput $input,
    ) {
    }

    public function __invoke(DataContainer $dc): string
    {
        $row = $this->repository->find((int) $this->input->query('id'));
        $path = null !== $row && CheckRepository::STATUS_DONE === $row['status'] ? $this->pdf->generate($row) : null;

        if (null === $path) {
            throw new ResponseException(new Response('', Response::HTTP_NOT_FOUND));
        }

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, basename($path));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Cache-Control', 'no-store');

        throw new ResponseException($response);
    }
}
