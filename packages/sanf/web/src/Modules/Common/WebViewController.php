<?php

namespace Sanf\Web\Modules\Common;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use League\Fractal\Resource\Collection;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Setting\Dtos\DetailFrequentlyAskQuestionCategoryPageDto;
use Sanf\Core\Modules\Setting\Dtos\ListFrequentlyAskQuestionCategoryPageDto;
use Sanf\Core\Modules\Setting\Dtos\ListFrequentlyAskQuestionPageDto;
use Sanf\Core\Modules\Setting\Services\DetailFrequentlyAskQuestionCategoryPageService;
use Sanf\Core\Modules\Setting\Services\ListFrequentlyAskQuestionCategoryPageService;
use Sanf\Core\Modules\Setting\Services\ListFrequentlyAskQuestionPageService;
use Sanf\Core\Modules\User\Services\GetPersonalAssistantUserService;
use Sanf\Web\Modules\Common\Transformers\SimpleFrequentlyAskQuestionCategoryPageTransformer;
use Sanf\Web\Modules\Common\Transformers\SimpleFrequentlyAskQuestionTransformer;

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

    public function browseFrequentlyAskQuestion(
        Guard $auth,
        Request $request,
        ListFrequentlyAskQuestionPageService $faqService,
        ListFrequentlyAskQuestionCategoryPageService $faqCategoryService,
        GetPersonalAssistantUserService $personalAssistantUserService
    ) {
        $inputs = $this->validate($request, [
            'is_popular' => 'nullable|boolean',
            'keyword' => 'nullable|string|regex:/^[a-zA-Z0-9 ]+$/',
            'limit' => 'nullable|integer',
            'skip' => 'nullable|integer',
            'sort_by' => ['nullable', Rule::in(['titleAsc', 'titleDesc', 'orderAsc', 'orderDesc',])],
        ]);

        $keyword = $inputs['keyword'] ?? null;
        $faqRequest = new ListFrequentlyAskQuestionPageDto([
            'isPopular' => $inputs['is_popular'] ?? false,
            'keyword' => $keyword,
            'limit' => $inputs['limit'] ?? null,
            'skip' => $inputs['skip'] ?? null,
            'sortBy' => $inputs['sortBy'] ?? null,
        ]);
        $faqResult = $faqService->execute($faqRequest);
        $faqs = new Collection($faqResult, SimpleFrequentlyAskQuestionTransformer::class);
        $faqs = $faqs->getData();

        $faqCategoryRequest = new ListFrequentlyAskQuestionCategoryPageDto([
            'keyword' => $keyword
        ]);
        $faqCategoryResult = $faqCategoryService->execute($faqCategoryRequest);
        $faqCategories = new Collection($faqCategoryResult, SimpleFrequentlyAskQuestionCategoryPageTransformer::class);
        $faqCategories = $faqCategories->getData();

        $personalAssistant = $personalAssistantUserService->execute((object)['userId' => $auth->id()]);

        return view(
            'web::web-view.faq.faq',
            compact('faqCategories', 'faqs', 'keyword', 'personalAssistant')
        );
    }

    public function browsePopularFrequentlyAskQuestion(
        Guard $auth,
        ListFrequentlyAskQuestionPageService $faqService,
        GetPersonalAssistantUserService $personalAssistantUserService
    ) {
        $faqRequest = new ListFrequentlyAskQuestionPageDto(['isPopular' => true]);
        $faqResult = $faqService->execute($faqRequest);
        $faqs = new Collection($faqResult, SimpleFrequentlyAskQuestionTransformer::class);
        $faqs = $faqs->getData();

        $personalAssistant = $personalAssistantUserService->execute((object)['userId' => $auth->id()]);

        return view(
            'web::web-view.faq.faq-popular',
            compact('faqs', 'personalAssistant')
        );
    }

    public function browseFrequentlyAskQuestionByCategory(
        Guard $auth,
        Request $request,
        $categoryId,
        ListFrequentlyAskQuestionPageService $faqService,
        DetailFrequentlyAskQuestionCategoryPageService $faqCategoryService,
        GetPersonalAssistantUserService $personalAssistantUserService
    ) {
        $inputs = $this->validate($request, [
            'keyword' => 'nullable|string|regex:/^[a-zA-Z0-9 ]+$/',
        ]);

        $faqCategoryRequest = new DetailFrequentlyAskQuestionCategoryPageDto(['id' => (int) $categoryId]);

        $faqCategoryResult = $faqCategoryService->execute($faqCategoryRequest);

        if (is_null($faqCategoryResult)) {
            return $this->faqNotFound();
        }

        $faqCategory = fractal($faqCategoryResult, SimpleFrequentlyAskQuestionCategoryPageTransformer::class);
        $faqCategory = (object)$faqCategory->toArray();

        $keyword = $inputs['keyword'] ?? null;
        $faqRequest = new ListFrequentlyAskQuestionPageDto([
            'keyword' => $keyword,
            'categoryId' => (int) $categoryId
        ]);
        $faqResult = $faqService->execute($faqRequest);
        $faqs = new Collection($faqResult, SimpleFrequentlyAskQuestionTransformer::class);
        $faqs = $faqs->getData();

        $personalAssistant = $personalAssistantUserService->execute((object)['userId' => $auth->id()]);

        return view(
            'web::web-view.faq.faq-by-category',
            compact('faqs', 'faqCategory', 'keyword', 'personalAssistant')
        );
    }

    private function faqNotFound()
    {
        return view('web::web-view.faq.faq-not-found');
    }
}
