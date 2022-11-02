<?php

namespace Sanf\Core\Modules\Setting\Services;

use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Repositories\StaticContentRepositoryInterface;

class GetStaticContentService implements ApplicationServiceInterface
{
    protected StaticContentRepositoryInterface $repository;

    public function __construct(StaticContentRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $staticContent = $this->repository->findByXid($dto->xid);
        if (!$staticContent) {
            abort(404);
        }

        if ($staticContent->started_at) {
            $date = date_localized($staticContent->started_at, '%d %B %Y');
        }
        if ($staticContent->ended_at) {
            if ($staticContent->started_at) {
                $date .= ' - ' . date_localized($staticContent->ended_at, '%d %B %Y');
            } else {
                $date = date_localized($staticContent->ended_at, '%d %B %Y');
            }
        }

        return (object) [
            'xid' => $staticContent->xid,
            'heading' => $staticContent->heading ?? null,
            'subHeading' => $staticContent->sub_heading ?? null,
            'body' => $staticContent->body ?? null,
            'imageHeader' => $staticContent->image_header ?? null,
            'imageFooter' => $staticContent->image_footer ?? null,
            'date' => $date ?? null,
        ];
    }
}
