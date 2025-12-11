<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Sanf\Api\Middleware\InjectUserIdFromPathMiddleware;

// ONLY PIC ROUTES
Route::group(['prefix' => 'v1', 'middleware' => ['auth']], function () {
    Route::get('on-boarding', ['as' => 'on-boarding.browse', 'uses' => 'Setting\OnBoardingController@getBrowse']);
});

Route::group(['prefix' => 'v1', 'middleware' => ['auth', 'pic']], function () {
    Route::post('users/financing-applications/company', ['as' => 'financing-applications.company.create', 'uses' => 'Financing\Controllers\FinancingApplicationByUserController@postAddByCompanyProfile']);
    Route::post('users/survey-submissions', ['as' => 'users.survey-submissions.add', 'uses' => 'Survey\Controllers\SurveyByUserController@add']);
    Route::post('users/profiles/{xid}/company/update', ['as' => 'users.profiles.company.update', 'uses' => 'User\Controllers\ProfileController@putUpdateCompanyProfile']);
    Route::post('users/profiles/{xid}/plafonds', ['as' => 'users.plafonds.create', 'uses' => 'Plafond\Controllers\PlafondController@postAddByUserProfile']);
    Route::post('users/profiles/{xid}/plafonds/increase', ['as' => 'users.plafonds.increase', 'uses' => 'Plafond\Controllers\PlafondController@postIncreaseByUserProfile']);
    Route::post('users/profiles/{xid}/contracts/{contract_no}/financing-units/{serial_no}/location-submissions', ['as' => 'users.contracts-financing-unit-location-submissions.add', 'uses' => 'Contract\Controllers\FinancingUnitLocationSubmissionByUserController@postAdd']);
    Route::post('users/profiles/{xid}/insurance-claim-submissions', ['as' => 'users.insurance-claim-submissions.add', 'uses' => 'Insurance\Controllers\InsuranceClaimSubmissionByUserController@postAdd']);
    Route::post('users/profiles/{xid}/invoice-collection-submissions', ['as' => 'users.invoice-collection-submissions.add', 'uses' => 'Invoice\Controllers\InvoiceCollectionSubmissionByUserController@postAdd']);
    Route::post('users/profiles/{xid}/prepayment-submissions', ['as' => 'users.prepayment-submissions.add', 'uses' => 'Prepayment\Controllers\PrepaymentSubmissionByUserController@postAdd']);
    Route::post('users/profiles/{xid}/shareholders/{no}/update', ['as' => 'users.shareholders.update', 'uses' => 'Shareholder\ShareholderController@putUpdate']);
    Route::post('users/profiles/{xid}/shareholders/{no}/delete', ['as' => 'users.shareholders.delete', 'uses' => 'Shareholder\ShareholderController@delete']);
    Route::post('users/profiles/{xid}/shareholders', ['as' => 'users.shareholders.create', 'uses' => 'Shareholder\ShareholderController@postCreate']);
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth'], function () {

    Route::post('assets', ['as' => 'assets.upload', 'uses' => 'Asset\AssetFileController@upload']);

    Route::get('products', ['as' => 'products.list', 'uses' => 'Product\ListProductController@process']);

    Route::get('contact-us/topics', ['as' => 'contact-us.topic', 'uses' => 'ContactUs\ListAskUsTopicController@process']);
    Route::post('contact-us/ask-us', ['as' => 'contact-us.ask-us', 'uses' => 'ContactUs\AskUsSubmitController@process']);

    Route::get('branch-offices', ['as' => 'branch.list', 'uses' => 'Branch\ListBranchController@process']);

    Route::get('customer-positions', ['as' => 'customer.positions', 'uses' => 'User\Controllers\PositionController@getList']);

    Route::get('provinces', ['as' => 'provinces.list', 'uses' => 'Location\CoreLocationController@provinces']);
    Route::get('provinces/{province_id}/cities', ['as' => 'cities.list', 'uses' => 'Location\CoreLocationController@cities']);
    Route::get('provinces/{province_id}/cities/{city_id}/districts', ['as' => 'districts.list', 'uses' => 'Location\CoreLocationController@districts']);
    Route::get('provinces/{province_id}/cities/{city_id}/districts/{district_name}', ['as' => 'sub-districts.list', 'uses' => 'Location\CoreLocationController@subDistricts']);

    Route::post('users/register-with-contract', ['as' => 'users.register-with-contract', 'uses' => 'User\Controllers\ProfileController@postRegisterWithContract']);
    Route::get('users/profiles', ['as' => 'users.profiles.list', 'uses' => 'User\Controllers\ProfileController@getList']);
    Route::post('users/profiles/{xid}/update', ['as' => 'users.profiles.update', 'uses' => 'User\Controllers\ProfileController@putUpdatePersonalProfile']);
    Route::get('users/profiles/{xid}', ['as' => 'users.profiles.detail', 'uses' => 'User\Controllers\ProfileController@getDetail']);
    Route::post('users/profile/{xid}/assets', ['as' => 'financing.assets', 'uses' => 'User\Controllers\ProfileAssetController@postUpload']);

    //TODO REFACTOR
    Route::get('users/profiles/{xid}/has-valid-ktp', ['as' => 'users.profiles.validate-ktp', 'uses' => 'Financing\Controllers\FinancingCompletionController@validateKtp']);
    Route::get('users/profiles/{xid}/has-valid-npwp', ['as' => 'users.profiles.validate-npwp', 'uses' => 'Financing\Controllers\FinancingCompletionController@validateNpwp']);
    Route::post('users/profiles/{xid}/company', ['as' => 'users.profiles.company.create', 'uses' => 'User\Controllers\ProfileController@postCreateCompanyProfile']);
    Route::post('users/profiles/{xid}/switch', ['as' => 'users.profiles.switch', 'uses' => 'User\Controllers\ProfileController@postSwitch']);

    Route::get('users/profiles/{xid}/shareholders', ['as' => 'users.shareholders.list', 'uses' => 'Shareholder\ShareholderController@getList']);
    Route::get('users/profiles/{xid}/shareholders/{no}', ['as' => 'users.shareholders.detail', 'uses' => 'Shareholder\ShareholderController@getDetail']);

    Route::get('users/profiles/{xid}/staffs', ['as' => 'users.staffs.list', 'uses' => 'Staff\StaffController@getList']);
    Route::get('users/profiles/{xid}/staffs/invited', ['as' => 'users.staffs.list-invited', 'uses' => 'Staff\StaffController@getInvitedList']);
    Route::post('users/profiles/{xid}/staffs/{no}/activate', ['as' => 'users.staffs.activate', 'uses' => 'Staff\StaffController@postActivate']);
    Route::post('users/profiles/{xid}/staffs/{no}/deactivate', ['as' => 'users.staffs.deactivate', 'uses' => 'Staff\StaffController@postDeactivate']);

    Route::get('customer-titles', ['as' => 'customer.titles', 'uses' => 'User\Controllers\TitleController@getList']);

    Route::get('news', ['as' => 'news.list', 'uses' => 'News\NewsController@getList']);

    Route::get('promo', ['as' => 'promo.list', 'uses' => 'Promo\PromoController@getList']);

    Route::get('astra-products', ['as' => 'astra.product.list', 'uses' => 'Astra\ProductAstraListController@getList']);

    Route::get('locations', ['as' => 'locations.list', 'uses' => 'Location\LocationController@getList']);

    Route::get('users/metadata-info', ['as' => 'users.metadata-info', 'uses' => 'User\Controllers\UserController@getProjectMetadataInfo']);
    Route::get('users/metadata-financing', ['as' => 'users.metadata-financing', 'uses' => 'User\Controllers\UserController@getFinancingMetadata']);

    // PROJECT
    Route::get('projects', ['as' => 'projects.list', 'uses' => 'Project\ProjectController@getList']);
    Route::get('projects/{xid}', ['as' => 'projects.detail', 'uses' => 'Project\ProjectController@getDetail']);
    Route::get('users/projects', ['as' => 'users.projects.list', 'uses' => 'Project\ProjectController@getListByUser']);
    Route::post('users/projects', ['as' => 'users.projects.list', 'uses' => 'Project\ProjectController@postCreateByUser']);
    Route::get('users/projects/{xid}', ['as' => 'users.projects.detail', 'uses' => 'Project\ProjectController@getDetailByUser']);
    Route::post('users/projects/{xid}/update', ['as' => 'users.projects.update', 'uses' => 'Project\ProjectController@putUpdateByUser']);
    Route::post('users/projects/{xid}/delete', ['as' => 'users.projects.delete', 'uses' => 'Project\ProjectController@deleteByUser']);
    Route::post('users/projects/{xid}/publish', ['as' => 'users.projects.publish', 'uses' => 'Project\ProjectController@postPublishByUser']);
    Route::post('users/projects/{xid}/unpublish', ['as' => 'users.projects.unpublish', 'uses' => 'Project\ProjectController@postUnpublishByUser']);

    // COMMODITY
    Route::get('commodities', ['as' => 'commodities.list', 'uses' => 'Commodity\CommodityController@getList']);
    Route::get('commodities/{xid}', ['as' => 'commodities.detail', 'uses' => 'Commodity\CommodityController@getDetail']);
    Route::get('users/commodities', ['as' => 'users.commodities.list', 'uses' => 'Commodity\CommodityController@getListByUser']);
    Route::post('users/commodities', ['as' => 'users.commodities.list', 'uses' => 'Commodity\CommodityController@postCreateByUser']);
    Route::get('users/commodities/{xid}', ['as' => 'users.commodities.detail', 'uses' => 'Commodity\CommodityController@getDetailByUser']);
    Route::post('users/commodities/{xid}/update', ['as' => 'users.commodities.update', 'uses' => 'Commodity\CommodityController@putUpdateByUser']);
    Route::post('users/commodities/{xid}/delete', ['as' => 'users.commodities.delete', 'uses' => 'Commodity\CommodityController@deleteByUser']);
    Route::post('users/commodities/{xid}/publish', ['as' => 'users.commodities.publish', 'uses' => 'Commodity\CommodityController@postPublishByUser']);
    Route::post('users/commodities/{xid}/unpublish', ['as' => 'users.commodities.unpublish', 'uses' => 'Commodity\CommodityController@postUnpublishByUser']);

    Route::get('users/profiles/{xid}/personal-assistants', ['as' => 'users.metadata-info', 'uses' => 'User\Controllers\UserController@getPersonalAssistant']);

    Route::get('financing-object-brands', ['as' => 'financing.personal.facility.brand', 'uses' => 'Financing\Controllers\FinancingObjectController@getBrands']);
    Route::get('financing-object-brands/{brand_id}/types', ['as' => 'financing.personal.facility.type', 'uses' => 'Financing\Controllers\FinancingObjectController@getTypes']);
    Route::get('financing-object-brands/{brand_id}/types/{type_id}/models', ['as' => 'financing.personal.facility.models', 'uses' => 'Financing\Controllers\FinancingObjectController@getModels']);
    Route::get('financing-methods', ['as' => 'financing-method.list', 'uses' => 'Financing\Controllers\FinancingController@getListMethods']);
    Route::get('financing-facilities', ['as' => 'financing-facilities.list', 'uses' => 'Financing\Controllers\FinancingController@getListFacilities']);
    Route::get('financing-facilities/{id}/methods', ['as' => 'financing-facilities.method.list', 'uses' => 'Financing\Controllers\FinancingController@getListMethodsByFacility']);
    Route::get('financing-prerequisites', ['as' => 'financing-prerequisites.list', 'uses' => 'Financing\Controllers\FinancingController@getPrerequisiteList']);
    Route::post('financing-simulations', ['as' => 'financing-simulations.create', 'uses' => 'Financing\Controllers\FinancingController@postCalculateSimulation']);
    Route::get('tnc-financing-application', ['as' => 'financing.tnc', 'uses' => 'Financing\Controllers\FinancingObjectController@getTNC']);
    Route::get('financing-simulation-categories', ['as' => 'financing-simulations.categories.list', 'uses' => 'Financing\Controllers\FinancingController@browseCategories']);
    Route::post('financing-simulations/financing-lease', ['as' => 'financing-simulations.financing-lease.calculate', 'uses' => 'Financing\Controllers\FinancingController@calculateFinancingLease']);
    Route::post('financing-simulations/credit-buying', ['as' => 'financing-simulations.credit-buying.calculate', 'uses' => 'Financing\Controllers\FinancingController@calculateCreditBuying']);
    Route::post('financing-simulations/sale-lease-back', ['as' => 'financing-simulations.sale-lease-back.calculate', 'uses' => 'Financing\Controllers\FinancingController@calculateSaleLeaseBack']);
    Route::post('financing-simulations/business-capital-facilities', ['as' => 'financing-simulations.business-capital-facilities', 'uses' => 'Financing\Controllers\FinancingController@calculateBusinessCapitalFacilities']);
    Route::post('financing-simulations/collateral-factoring', ['as' => 'financing-simulations.collateral-factoring', 'uses' => 'Financing\Controllers\FinancingController@calculateCollateralFactoring']);
    Route::post('financing-simulations/unsecured-factoring', ['as' => 'financing-simulations.unsecured-factoring', 'uses' => 'Financing\Controllers\FinancingController@calculateUnSecuredFactoring']);
    Route::post('financing/first-year-insurance-amount', ['as' => 'financing.first-year-insurance.calculate', 'uses' => 'Financing\Controllers\FinancingController@calculateFirstYearInsurance']);
    Route::post('financing/provision-amount', ['as' => 'financing.provision-amount.calculate', 'uses' => 'Financing\Controllers\FinancingController@calculateProvision']);

    Route::get('users/profiles/{xid}/financing-applications', ['as' => 'financing-applications.list', 'uses' => 'Financing\Controllers\FinancingApplicationByUserController@getBrowse']);
    Route::get('users/profiles/{xid}/financing-applications/{application_xid}', ['as' => 'financing-applications.detail', 'uses' => 'Financing\Controllers\FinancingApplicationByUserController@getRead']);
    Route::post('users/financing-applications/personal', ['as' => 'financing-applications.personal.create', 'uses' => 'Financing\Controllers\FinancingApplicationByUserController@postAddByPersonalProfile']);

    // PLAFOND
    Route::get('plafond-types', ['as' => 'plafond-types', 'uses' => 'Plafond\Controllers\PlafondController@getBrowseTypesOldest']);
    Route::get('users/profiles/{xid}/plafonds', ['as' => 'users.plafonds.list', 'uses' => 'Plafond\Controllers\PlafondController@getBrowseByUserProfile']);
    Route::get('users/profiles/{xid}/plafonds/histories', ['as' => 'users.plafonds.histories.list', 'uses' => 'Plafond\Controllers\PlafondController@getBrowseHistoryByUserProfile']);
    Route::get('users/profiles/{xid}/plafonds/types/{typeId}', ['as' => 'users.plafonds.detail-by-type', 'uses' => 'Plafond\Controllers\PlafondController@getReadByUserProfileAndType']);
    Route::get('users/profiles/{xid}/plafonds/factoring', ['as' => 'users.plafonds.factoring', 'uses' => 'Plafond\Controllers\PlafondController@browsePlafondFactoring']);
    Route::post('users/profiles/{xid}/plafonds/factorings/{plafond_xid}/disbursements', ['as' => 'users.plafonds.factorings.disbursement.add', 'uses' => 'Plafond\Controllers\PlafondFactoringDisbursementController@add']);
    Route::get('users/profiles/{xid}/plafonds/factorings/{plafond_xid}/disbursements', ['as' => 'users.plafonds.factorings.disbursement.browse', 'uses' => 'Plafond\Controllers\PlafondFactoringDisbursementController@browse']);
    Route::post('users/profiles/{xid}/plafonds/factorings/{plafond_xid}/disbursements/{disbursement_xid}', ['as' => 'users.plafonds.factorings.disbursement.update', 'uses' => 'Plafond\Controllers\PlafondFactoringDisbursementController@update']);
    Route::get('users/profiles/{xid}/plafonds/factorings/{plafond_xid}/disbursements/{disbursement_xid}', ['as' => 'users.plafonds.factorings.disbursement.read', 'uses' => 'Plafond\Controllers\PlafondFactoringDisbursementController@read']);
    Route::post('users/profiles/{xid}/plafonds/invoice/upload', ['as' => 'users.plafonds.invoice.upload', 'uses' => 'Plafond\Controllers\InvoicePlafondController@uploadDocument']);
    Route::post('users/profiles/{xid}/plafonds/invoice/scan', ['as' => 'users.plafonds.invoice.scan', 'uses' => 'Plafond\Controllers\InvoicePlafondController@scanOCRDocument']);
    Route::post('users/profiles/{xid}/plafonds/factorings/{plafond_xid}/payment-accelaration-document/email', ['as' => 'v1.plafond.payment-accelaration-document.email', 'uses' => 'Plafond\Controllers\PlafondDocumentController@sendPaymentAccelarationDocument']);
    Route::post('users/profiles/{xid}/plafonds/factorings/{plafond_xid}/payment-accelaration-document/print', ['as' => 'v1.plafond.payment-accelaration-document.print', 'uses' => 'Plafond\Controllers\PlafondDocumentController@printPaymentAccelarationDocument']);
    Route::get('users/profiles/{xid}/plafonds/factorings/{plafond_xid}/payment-accelaration-document/download', ['as' => 'v1.plafond.payment-accelaration-document.print', 'uses' => 'Plafond\Controllers\PlafondDocumentController@downloadPaymentAccelarationDocument']);
    // CR 2025
    Route::post('users/profiles/{xid}/plafonds/payment-accelaration-document/scan-upload', ['as' => 'v1.plafonds.payment-accelaration-document.scan-upload', 'uses' => 'Plafond\Controllers\PaymentAccDocController@scanOCRUploadDocument']);

    // E-SIGN
    Route::get('users/profiles/{xid}/contracts/esign-user', ['as' => 'users.contracts.esign-user', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@getUser']);
    Route::post('users/profiles/{xid}/contracts/esign-register', ['as' => 'users.contracts.esign-registration', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@postRegistration']);
    Route::post('users/profiles/{xid}/contracts/esign-registration-check', ['as' => 'users.contracts.esign-user', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@postRegistrationCheck']);
    Route::post('users/profiles/{xid}/contracts/esign-send-verification', ['as' => 'users.contracts.esign-user', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@postResendVerification']);
    Route::get('users/profiles/{xid}/contracts/esign-files', ['as' => 'users.contracts.esign-files', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@getBrowse']);
    Route::post('users/profiles/{xid}/contracts/esign-files/{document_id}/generate-url', ['as' => 'users.contracts.esign-files.url', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@postGenerateSignUrl']);
    Route::post('users/profiles/{xid}/contracts/esign-files/{document_id}/signed', ['as' => 'users.contracts.esign-files.signed', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@postDocumentSigned']);
    Route::post('users/profiles/{xid}/contracts/esign-files/{document_id}/send', ['as' => 'users.contracts.esign-files.send-mail', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@postSendDocumentViaEmail']);
    Route::get('users/profiles/{xid}/contracts/provinces', ['as' => 'users.contracts.master-data.provinces', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@getProvinces']);
    Route::get('users/profiles/{xid}/contracts/provinces/{provinceXid}/districts', ['as' => 'users.contracts.master-data.districts', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@getDistricts']);
    Route::get('users/profiles/{xid}/contracts/provinces/{provinceXid}/districts/{districtXid}/subdistrict', ['as' => 'users.contracts.master-data.sub-districts', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@getSubDistricts']);

    // CONTRACT
    Route::get('users/profiles/{xid}/metadata-contract', ['as' => 'users.metadata-contract', 'uses' => 'User\Controllers\ProfileController@getMetadataContract']);
    Route::get('users/profiles/{xid}/metadata-account-receivable', ['as' => 'users.metadata-account-receivable', 'uses' => 'User\Controllers\ProfileController@getMetadataAccountReceivable']);
    Route::get('users/profiles/{xid}/account-receivables/info', ['as' => 'users.account-receivables.info', 'uses' => 'Contract\Controllers\AccountReceivableByUserController@getInfo']);
    Route::get('users/profiles/{xid}/contract-post-dated-cheques', ['as' => 'users.contract-pdc.browse', 'uses' => 'Contract\Controllers\PostDatedChequeByUserController@getContract']);
    Route::get('users/profiles/{xid}/contracts/{contract_no}/post-dated-cheques', ['as' => 'users.contracts-pdc.read', 'uses' => 'Contract\Controllers\PostDatedChequeByUserController@getPDC']);
    Route::get('users/profiles/{xid}/contracts-financing-unit', ['as' => 'users.contracts-financing-object.browse', 'uses' => 'Contract\Controllers\FinancingUnitLocationSubmissionByUserController@getContract']);
    Route::get('users/profiles/{xid}/contracts/{contract_no}/financing-unit-location-submissions', ['as' => 'users.contracts-financing-unit-location-submissions.read', 'uses' => 'Contract\Controllers\FinancingUnitLocationSubmissionByUserController@getFinancingUnitLocation']);
    Route::get('users/profiles/{xid}/contracts', ['as' => 'users.contracts.browse', 'uses' => 'Contract\Controllers\ContractFinancingUnitByUserController@getListOldest']); //TODO remove after +1 release version
    Route::get('users/profiles/{xid}/contracts/{contract_no}', ['as' => 'users.contracts.read', 'uses' => 'Contract\Controllers\ContractFinancingUnitByUserController@getDetail']);
    Route::get('users/profiles/{xid}/contracts/{contract_no}/financing-units', ['as' => 'users.contracts-financing-object.browse', 'uses' => 'Contract\Controllers\ContractFinancingUnitByUserController@getFinancingUnit']);
    Route::get('users/profiles/{xid}/contracts/{contract_no}/penalties', ['as' => 'users.contracts.penalties', 'uses' => 'Contract\Controllers\ContractFinancingUnitByUserController@getPenalties']);

    Route::get('cities', ['as' => 'all-cities.list', 'uses' => 'Location\CoreLocationController@getCities']);

    // INVOICE
    Route::get('users/profiles/{xid}/financing-units-invoice-collection', ['as' => 'users.invoice-collection-financing-units.browse', 'uses' => 'Invoice\Controllers\FinancingUnitByUserController@getBrowse']);
    Route::get('users/profiles/{xid}/invoice-collection-submissions', ['as' => 'users.invoice-collection-submissions.browse', 'uses' => 'Invoice\Controllers\InvoiceCollectionSubmissionByUserController@getBrowse']);

    // INSURANCE
    Route::get('users/profiles/{xid}/financing-units-insurance-claim', ['as' => 'users.insurance-claim-financing-units.browse', 'uses' => 'Insurance\Controllers\FinancingUnitByUserController@getBrowse']);
    Route::get('users/profiles/{xid}/insurance-claim-submissions', ['as' => 'users.insurance-claim-submissions.browse', 'uses' => 'Insurance\Controllers\InsuranceClaimSubmissionByUserController@getBrowse']);
    Route::get('users/profiles/{xid}/insurance-claim-submissions/{submissionXid}', ['as' => 'users.insurance-claim-submissions.read', 'uses' => 'Insurance\Controllers\InsuranceClaimSubmissionByUserController@getRead']);

    // PREPAYMENT
    Route::get('users/profiles/{xid}/contracts-prepayment', ['as' => 'users.prepayment-contracts.browse', 'uses' => 'Prepayment\Controllers\ContractByUserController@getBrowse']);
    Route::post('users/prepayment-simulations', ['as' => 'users.prepayment-simulations.add', 'uses' => 'Prepayment\Controllers\PrepaymentSimulationByUserController@postAdd']);

    // NOTIFICATION
    Route::get('users/notifications', ['as' => 'users.notifications', 'uses' => 'Notification\NotificationByUserController@getBrowse']);
    Route::post('users/notifications/read/update', ['as' => 'users.notifications.read', 'uses' => 'Notification\NotificationByUserController@patchMarkAsRead']);

    // Survey
    Route::get('users/surveys', ['as' => 'users.surveys.browse', 'uses' => 'Survey\Controllers\SurveyByUserController@browse']);
    Route::get('users/surveys/{contract_no}', ['as' => 'users.surveys.detail', 'uses' => 'Survey\Controllers\SurveyByUserController@detail']);
    Route::get('users/survey-assignments', ['as' => 'users.survey-assignments.browse', 'uses' => 'Survey\Controllers\SurveyAssignmentByUserController@browse']);

    // PIN
    Route::post('users/add-pin', ['as' => 'users.pin.add', 'uses' => 'User\Controllers\AuthPinController@postAdd']);
    Route::post('users/check-pin', ['as' => 'users.pin.check', 'uses' => 'User\Controllers\AuthPinController@postCheck']);
    Route::post('users/update-pin/update', ['as' => 'users.pin.update', 'uses' => 'User\Controllers\AuthPinController@postUpdate']);
    Route::post('users/request-forgot-pin', ['as' => 'users.pin.request-forgot', 'uses' => 'User\Controllers\AuthPinController@postRequestForgot']);
    Route::post('users/reset-pin', ['as' => 'users.pin.reset', 'uses' => 'User\Controllers\AuthPinController@postReset']);

    Route::post('users/request-deactivation', ['as' => 'users.deactivate', 'uses' => 'User\Controllers\AuthUserControllerByUser@postDeactivate']);

    // REQUEST DOCUMENT UPLOAD
    Route::get('users/profiles/{xid}/requests-document', ['as' => 'v1.users.request-document.browse', 'uses' => 'RequestedDocument\Controllers\RequestedDocumentByUserController@getList']);
    Route::get('users/profiles/{xid}/requests-document/{request_id}', ['as' => 'v1.users.request-document.read', 'uses' => 'RequestedDocument\Controllers\RequestedDocumentByUserController@getRead']);
    Route::post('users/profiles/{xid}/requests-document/{request_id}/documents/submit', ['as' => 'v1.users.request-document-history.submit', 'uses' => 'RequestedDocument\Controllers\RequestedDocumentByUserController@postSubmit']);
    Route::post('users/profiles/{xid}/requests-document/{request_id}/documents/{document_id}', ['as' => 'v1.users.request-document-history.upload', 'uses' => 'RequestedDocument\Controllers\RequestedDocumentByUserController@postUpload']);
    Route::get('users/profiles/{xid}/requests-document/{request_id}/documents/{document_id}', ['as' => 'v1.users.request-document-history.browse', 'uses' => 'RequestedDocument\Controllers\RequestedDocumentByUserController@getHistory']);

    // OCR
    Route::get('users/profiles/{xid}/permission/ocr', ['as' => 'v1.users.ocr.permission.read', 'uses' => 'Ocr\Controllers\OCRController@getUserPermission']);

    // BANK
    Route::get('users/profiles/{xid}/banks', ['as' => 'v1.users.bank.account.browse', 'uses' => 'Bank\Controllers\BankController@getUserAccount']);

    // Ad-Ins e-SignHub
    Route::post('users/profiles/{xid}/contracts/esign-otp', ['as' => 'users.contracts.esign-otp', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@requestOtp']);
    Route::get('users/profiles/{xid}/contracts/esign-files/{document_id}/download', ['as' => 'users.contracts.esign-files.download', 'uses' => 'Contract\Controllers\ESignDocumentByUserController@downloadDocument']);

    // PDC HOLD GIRO (CR 2025)
    Route::get('pdc-hold-reasons', ['as' => 'v1.pdc-hold-reasons.browse', 'uses' => 'PdcHold\Controllers\PdcHoldReasonController@getList']);
    Route::post('users/profiles/{xid}/pdc-hold-multi-giro', ['as' => 'v1.pdc-hold-multi-giro.add', 'uses' => 'PdcHold\Controllers\PdcHoldController@postAddMultiGiro']);
    Route::post('users/profiles/{xid}/pdc-hold-multi-contract', ['as' => 'v1.pdc-hold-multi-contract.add', 'uses' => 'PdcHold\Controllers\PdcHoldController@postAddMultiContract']);
    Route::post('users/profiles/{xid}/pdc-resume', ['as' => 'v1.pdc-resume.add', 'uses' => 'PdcHold\Controllers\PdcHoldController@postResume']);
    Route::get('users/profiles/{xid}/pdc-holds', ['as' => 'v1.pdc-hold.browse', 'uses' => 'PdcHold\Controllers\PdcHoldController@getBrowse']);
    Route::get('users/profiles/{xid}/pdc-holds/{submissionXid}', ['as' => 'users.pdc-hold.read', 'uses' => 'PdcHold\Controllers\PdcHoldController@getRead']);
    Route::post('users/profiles/{xid}/giro-post-dated-cheques', ['as' => 'users.giro-pdc.browse', 'uses' => 'Contract\Controllers\PostDatedChequeByUserController@getPDCV2']);
});

Route::group(['prefix' => 'v2', 'middleware' => 'auth'], function () {
    //TODO remove after +1 release version
    Route::get('plafond-types', ['as' => 'v2.plafond-types', 'uses' => 'Plafond\Controllers\PlafondController@getBrowseTypes']);
    Route::get('users/profiles/{xid}/contracts', ['as' => 'v2.users.contracts.browse', 'uses' => 'Contract\Controllers\ContractFinancingUnitByUserController@getList']);

    // Ad-Ins e-SignHub
    Route::post('users/profiles/{xid}/contracts/esign-register', ['as' => 'v2.users.contracts.esign-register', 'uses' => 'Contract\Controllers\ESignDocumentV2Controller@registration']);
    Route::get('users/profiles/{xid}/contracts/esign-user', ['as' => 'v2.users.contracts.esign-user', 'uses' => 'Contract\Controllers\ESignDocumentV2Controller@getUser']);
    Route::post('users/profiles/{xid}/contracts/esign-files/{document_id}/signed', ['as' => 'v2.users.contracts.esign-files.signed', 'uses' => 'Contract\Controllers\ESignDocumentV2Controller@signing']);

    // CR 2025
    Route::get('users/profiles/{xid}/financing-units-invoice-collection', ['as' => 'v2.users.invoice-collection-financing-units.browse', 'uses' => 'Invoice\Controllers\FinancingUnitByUserV2Controller@getBrowse']);

    Route::get('cities', ['as' => 'v2.all-cities.list', 'uses' => 'Location\CoreLocationV2Controller@getCities']);

    Route::post('users/profiles/{xid}/insurance-claim-submissions', ['as' => 'v2.users.insurance-claim-submissions.add', 'uses' => 'Insurance\Controllers\InsuranceClaimSubmissionByUserController@postAddV2']);
});

// SCANINA INTEGRATION
Route::group(['prefix' => 'v1', 'middleware' => ['auth']], function () {
    Route::post('users/profiles/{xid}/scanina-account', ['as' => 'scanina.user.check', 'uses' => \Scanina\Controllers\User\GetUserAccountController::class]);
    Route::post('users/profiles/{xid}/scanina-account/resend', ['as' => 'scanina.user.check', 'uses' => \Scanina\Controllers\User\ResendUserMailVerificationController::class]);
    Route::post('users/profiles/{xid}/scanina-account/register', ['as' => 'scanina.user.register', 'uses' => \Scanina\Controllers\User\RegisterScaninaUserController::class]);
    Route::get('users/profiles/{xid}/scanina/products/buys', ['as' => 'scanina.user.product.buy.browse', 'uses' => \Scanina\Controllers\User\BrowseBuyCartByUserController::class]);
    Route::get('users/profiles/{xid}/scanina/products/rents', ['as' => 'scanina.user.product.rent.browse', 'uses' => \Scanina\Controllers\User\BrowseRentCartByUserController::class]);
    Route::get('users/profiles/{xid}/scanina/products/spare-parts', ['as' => 'scanina.user.product.spare-part.browse', 'uses' => \Scanina\Controllers\User\BrowseSparePartCartByUserController::class]);
    Route::get('users/profiles/{xid}/scanina/products/services', ['as' => 'scanina.user.product.service.browse', 'uses' => \Scanina\Controllers\User\BrowseServiceCartByUserController::class]);
    Route::post('users/profiles/{xid}/scanina/products/buys/{product_xid}', ['as' => 'scanina.user.product.buy.cart', 'uses' => \Scanina\Controllers\User\AddBuyCartByUserController::class]);
    Route::get('users/profiles/{xid}/scanina/products/buys/{product_xid}', ['as' => 'scanina.user.product.buy.read', 'uses' => \Scanina\Controllers\User\ReadBuyCartByUserController::class]);
    Route::post('users/profiles/{xid}/scanina/products/rents/{product_xid}', ['as' => 'scanina.user.product.rent.cart', 'uses' => \Scanina\Controllers\User\AddRentCartByUserController::class]);
    Route::get('users/profiles/{xid}/scanina/products/rents/{product_xid}', ['as' => 'scanina.user.product.rent.read', 'uses' => \Scanina\Controllers\User\ReadRentCartByUserController::class]);
    Route::post('users/profiles/{xid}/scanina/products/spare-parts/{product_xid}', ['as' => 'scanina.user.product.spare-part.cart', 'uses' => \Scanina\Controllers\User\AddSparePartCartByUserController::class]);
    Route::get('users/profiles/{xid}/scanina/products/spare-parts/{product_xid}', ['as' => 'scanina.user.product.spare-part.read', 'uses' => \Scanina\Controllers\User\ReadSparePartCartByUserController::class]);
    Route::post('users/profiles/{xid}/scanina/products/services/{product_xid}', ['as' => 'scanina.user.product.service.cart', 'uses' => \Scanina\Controllers\User\AddServiceCartByUserController::class]);
    Route::get('users/profiles/{xid}/scanina/products/services/{product_xid}', ['as' => 'scanina.user.product.service.read', 'uses' => \Scanina\Controllers\User\ReadServiceCartByUserController::class]);

    Route::get('scanina/banner', ['as' => 'scanina.banner.read', 'uses' => \Scanina\Controllers\ReadBannerController::class]);
    Route::get('scanina/countries', ['as' => 'scanina.country.browse', 'uses' => \Scanina\Controllers\Region\BrowseCountryController::class]);
    Route::get('scanina/cities', ['as' => 'scanina.cities.browse', 'uses' => \Scanina\Controllers\Region\BrowseCityController::class]);
    Route::get('scanina/business-sectors', ['as' => 'scanina.business-sector.browse', 'uses' => \Scanina\Controllers\Region\BrowseBusinessSectorController::class]);
    Route::get('scanina/merchants', ['as' => 'scanina.merchant.browse', 'uses' => \Scanina\Controllers\Region\BrowseMerchantController::class]);
    Route::post('scanina/products/buys', ['as' => 'scanina.product.buy.store', 'uses' => \Scanina\Controllers\Product\BrowseProductBuyController::class]);
    Route::get('scanina/products/buys', ['as' => 'scanina.product.buy.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductBuyController::class]);
    Route::get('scanina/products/buys/categories', ['as' => 'scanina.product.buy.category.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductBuyCategoryController::class]);
    Route::get('scanina/products/buys/brands', ['as' => 'scanina.product.buy.brand.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductBuyBrandController::class]);
    Route::get('scanina/products/buys/types', ['as' => 'scanina.product.buy.type.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductBuyTypeController::class]);
    Route::get('scanina/products/buys/models', ['as' => 'scanina.product.buy.model.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductBuyModelController::class]);
    Route::get('scanina/products/buys/{xid}', ['as' => 'scanina.product.buy.read', 'uses' => \Scanina\Controllers\Product\ReadProductBuyController::class]);
    Route::get('scanina/products/rents', ['as' => 'scanina.product.rent.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductRentController::class]);
    Route::get('scanina/products/rents/categories', ['as' => 'scanina.product.rent.category.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductRentCategoryController::class]);
    Route::get('scanina/products/rents/brands', ['as' => 'scanina.product.rent.brand.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductRentBrandController::class]);
    Route::get('scanina/products/rents/types', ['as' => 'scanina.product.rent.type.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductRentTypeController::class]);
    Route::get('scanina/products/rents/models', ['as' => 'scanina.product.rent.model.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductRentModelController::class]);
    Route::get('scanina/products/rents/{xid}', ['as' => 'scanina.product.rent.read', 'uses' => \Scanina\Controllers\Product\ReadProductRentController::class]);
    Route::get('scanina/products/spare-parts', ['as' => 'scanina.product.spare-part.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductSparePartController::class]);
    Route::get('scanina/products/spare-parts/categories', ['as' => 'scanina.product.spare-part.category.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductSparePartCategoryController::class]);
    Route::get('scanina/products/spare-parts/brands', ['as' => 'scanina.product.spare-part.brand.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductSparePartBrandController::class]);
    Route::get('scanina/products/spare-parts/{xid}', ['as' => 'scanina.product.spare-part.read', 'uses' => \Scanina\Controllers\Product\ReadProductSparePartController::class]);
    Route::get('scanina/products/spare-parts/{xid}/reviews', ['as' => 'scanina.product.spare-part.review.browse', 'uses' => \Scanina\Controllers\Product\BrowseReviewProductSparePartController::class]);
    Route::get('scanina/products/services', ['as' => 'scanina.product.service.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductServiceController::class]);
    Route::get('scanina/products/services/categories', ['as' => 'scanina.product.service.category.browse', 'uses' => \Scanina\Controllers\Product\BrowseProductServiceCategoryController::class]);
    Route::get('scanina/products/services/{xid}', ['as' => 'scanina.product.service.read', 'uses' => \Scanina\Controllers\Product\ReadProductServiceController::class]);
    Route::get('scanina/products/services/{xid}/reviews', ['as' => 'scanina.product.service.review.browse', 'uses' => \Scanina\Controllers\Product\BrowseReviewProductServiceController::class]);
});

// CR 2 2025
Route::group(['prefix' => 'v2', 'middleware' => ['auth', InjectUserIdFromPathMiddleware::class]], function () {
    Route::get('users/profiles/{xid}/spare_part_disbursements', ['as' => 'v2.users.spare_part_disbursements', 'uses' => 'Disbursement\Controllers\SparePartDisbursementController@list']);
});
