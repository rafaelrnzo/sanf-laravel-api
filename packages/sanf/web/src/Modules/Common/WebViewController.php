<?php


namespace Sanf\Web\Modules\Common;

use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Faq\Dtos\DetailFaqCategoryDto;
use Sanf\Core\Modules\Faq\Dtos\ListFaqCategoryDto;
use Sanf\Core\Modules\Faq\Dtos\ListFaqDto;
use Sanf\Core\Modules\Faq\Services\DetailFaqCategoryService;
use Sanf\Core\Modules\Faq\Services\ListFaqCategoryService;
use Sanf\Core\Modules\Faq\Services\ListFaqService;
use Sanf\Web\Modules\Common\Transformers\FaqCategoryTransformer;
use Sanf\Web\Modules\Common\Transformers\FaqTransformer;

class WebViewController extends RestApiController
{
    public function aboutUs()
    {
        return view('web::web-view.about-us');
    }

    public function termsCondition()
    {
        return view('web::web-view.terms-and-condition');
    }

    public function privacyPolicy()
    {
        return view('web::web-view.privacy-policy');
    }

    public function approvalCommodity($status)
    {
        switch ($status) {
            case 'approve':
                $message = 'Permintaan telah disetujui';
                break;
            case 'reject':
                $message = 'Permintaan tidak disetujui';
                break;
            default:
                abort(404);
        }

        return view('core::layouts.message', ['message' => $message]);
    }

    public function approvalProject($status)
    {
        switch ($status) {
            case 'approve':
                $message = 'Permintaan telah disetujui';
                break;
            case 'reject':
                $message = 'Permintaan tidak disetujui';
                break;
            default:
                abort(404);
        }

        return view('core::layouts.message', ['message' => $message]);
    }

    public function faq(
        Request                $request,
        ListFaqService         $faqService,
        ListFaqCategoryService $faqCategoryService
    )
    {
        $keyword = $request->get('keyword');

        $faqRequest = new ListFaqDto([
            'limit' => $keyword ? null : 5,
            'isPopular' => $keyword ? null : true,
            'searchKeyword' => $keyword,
        ]);
        $faqResult = $faqService->execute($faqRequest);
        $faqs = new Collection($faqResult, FaqTransformer::class);
        $faqs = $faqs->getData();

        $faqCategoryRequest = new ListFaqCategoryDto([
            'searchFaqKeyword' => $keyword
        ]);
        $faqCategoryResult = $faqCategoryService->execute($faqCategoryRequest);
        $faqCategories = new Collection($faqCategoryResult, FaqCategoryTransformer::class);
        $faqCategories = $faqCategories->getData();

        return view(
            'web::web-view.faq.faq',
            compact('faqCategories', 'faqs', 'keyword')
        );
    }

    public function faqByCategory(
        Request                  $request,
        ListFaqService           $faqService,
        DetailFaqCategoryService $faqCategoryService,
                                 $categoryId
    )
    {
        $keyword = $request->get('keyword');
        $categoryId = (int)$categoryId;

        $faqCategoryRequest = new DetailFaqCategoryDto(['id' => $categoryId]);

        $faqCategoryResult = $faqCategoryService->execute($faqCategoryRequest);

        if (is_null($faqCategoryResult)) {
            return $this->faqNotFound();
        }

        $faqCategory = fractal($faqCategoryResult, FaqCategoryTransformer::class);
        $faqCategory = (object)$faqCategory->toArray();

        $faqRequest = new ListFaqDto([
            'searchKeyword' => $keyword,
            'categoryId' => $categoryId
        ]);
        $faqResult = $faqService->execute($faqRequest);
        $faqs = new Collection($faqResult, FaqTransformer::class);
        $faqs = $faqs->getData();

        return view(
            'web::web-view.faq.faq-by-category',
            compact('faqs', 'faqCategory', 'keyword')
        );
    }

    public function faqPopular(ListFaqService $faqService)
    {
        $faqRequest = new ListFaqDto(['isPopular' => true]);
        $faqResult = $faqService->execute($faqRequest);
        $faqs = new Collection($faqResult, FaqTransformer::class);
        $faqs = $faqs->getData();

        return view(
            'web::web-view.faq.faq-popular',
            compact('faqs')
        );
    }

    private function faqNotFound()
    {
        return view('web::web-view.faq.faq-not-found');
    }
}
