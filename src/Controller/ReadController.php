<?php declare(strict_types=1);

/*
 * (c) Rob Bast <rob.bast@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Paste\Controller;

use Paste\Repository\PasteRepository;
use Paste\Repository\StorageException;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReadController
{
    /** @var PasteRepository */
    protected $repository;

    /** @var EngineInterface */
    private $engine;

    /**
     * @param EngineInterface $engine
     * @param PasteRepository $repository
     */
    public function __construct(EngineInterface $engine, PasteRepository $repository)
    {
        $this->engine = $engine;
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @param string $id
     * @param bool $raw
     *
     * @return Response
     */
    public function __invoke(Request $request, string $id, $raw = false): Response
    {
        try {
            $paste = $this->repository->find($id);
        } catch (StorageException $exception) {
            throw new NotFoundHttpException();
        }

        $accept = AcceptHeader::fromString($request->headers->get('Accept'));

        if ($accept->has('text/html') && !$raw) {
            $body = $this->engine->render('read.html.twig', ['paste' => $paste]);
            $headers = ['Content-Type' => 'text/html'];
        } else {
            $body = $paste;
            $headers = ['Content-Type' => 'text/plain'];
        }

        $response = new Response($body, 200, $headers);
        $response
            ->setVary(['Accept', 'Accept-Encoding'])
            ->setEtag(md5($response->getContent()))
            ->setTtl(3600)
            ->setClientTtl(300)
        ;

        if (!$request->isNoCache()) {
            $response->isNotModified($request);
        }

        return $response;
    }
}
