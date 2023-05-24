<?php

namespace Sanf\Integration\Modules\Scanina;

use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Core\Modules\Scanina\Dtos\AddToCartRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductBuyFilterDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductFilterDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductRentFilterDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductServiceFilterDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductSparePartFilterDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaUserRegisterRequestDto;

class ScaninaApiClient
{
    public const DEFAULT_SKIP = 0;
    public const DEFAULT_LIMIT = 2147483647;
    public const DEFAULT_ORDER = 'Latest';

    protected $client;

    public function __construct()
    {
        //TODO INJECT
        $this->client = app(\GuzzleHttp\Client::class);
    }

    public function getBuy(ScaninaProductBuyFilterDto $arguments)
    {
        $response = Request::route('scanina.product.buy.browse', $this->client)
            ->queryParams($arguments->toArray())
            ->send();

        return $response->json(false);
    }

    public function getRent(ScaninaProductRentFilterDto $arguments)
    {
        $response = Request::route('scanina.product.rent.browse', $this->client)
            ->queryParams($arguments->toArray())
            ->send();

        return $response->json(false);
    }

    public function getService(ScaninaProductServiceFilterDto $arguments)
    {
        $response = Request::route('scanina.product.service.browse', $this->client)
            ->queryParams($arguments->toArray())
            ->send();

        return $response->json(false);
    }

    public function getSparePart(ScaninaProductSparePartFilterDto $arguments)
    {
//        $response = Request::route('integration.scanina.product.spare-spart.browse', $this->client)
//            ->queryParams($arguments->toArray())
//            ->send();
//
//        return $response->json();

        return json_decode('{"success":true,"code":"200","message":"OK","data":{"rows":[{"xid":"HNS1231","shopId":"1","merchantId":"1","name":"Maintenance 300","nameSlug":"maintenance-300","description":"lorem ipsum","imageFiles":{"imageName":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.jpg","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"},"priceBefore":"200000000","price":"100000000","year":"2019","catalogId":"1","catalogName":"maintenance","stock":"1","rating":"3","reviewCount":"10","itemSoldCount":"12","createdAt":"1683601805","updatedAt":"1683601805"}],"metadata":{"total":1,"count":1,"skip":0,"limit":10,"sort_by":"latest"}}}');
    }

    public function readBuy(string $xid)
    {
//        $response = Request::route('integration.scanina.product.buy.read', $this->client)
//            ->pathParams([
//                'xid' => $xid,
//            ])
//            ->send();
//
//        return $response->json();

        return json_decode('{"success":true,"code":"200","message":"OK","data":{"id":"1","shopId":"1","shopName":"scan","merchantId":"1","merchantName":"scanina","serialNumber":"BY-SJJNM-122","name":"Bomang Type 1","nameSlug":"bomang-type-a1","description":"huge truck","imageFiles":[{"imageName":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.jpg","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"}],"videoFile":[{"fileName":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.jpg","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"}],"documentationFile":[{"fileName":"test.pdf","path":"https:/minio.nbs.co.id/dev"}],"priceBefore":"200000000","price":"100000000","year":"2019","catalogId":"1","catalogName":"truck","locationId":"1002171","locationName":"Batam","conditionTypeId":"1","conditionTypeName":"New","unitMeasurement":{"rate":12399,"measurement":"kilometer"},"stock":"1","rating":"3","viewCount":"38","lastSeen":"123123233","isQualified":"true","isAssurance":"true","latitude":"-6.30064100","longitude":"106.81409500","createdAt":"1683601805","updatedAt":"1683601805","review":{"total":10,"withImages":5,"customerSatisfied":[{"label":"Engine Perfomance","count":1},{"label":"Delivery Accuracy","count":1}],"customerUnstatisfied":[{"label":"Operator Perfomance","count":1},{"label":"Ability Accuracy","count":1}],"ratingProgress":{"0":10,"1":0,"2":0,"3":0,"4":0,"5":0}}}}');
    }

    public function readRent(string $xid)
    {
//        $response = Request::route('integration.scanina.product.rent.read', $this->client)
//            ->pathParams([
//                'xid' => $xid,
//            ])
//            ->send();
//
//        return $response->json();

        return json_decode('{"success":true,"code":"200","message":"OK","data":{"id":"1","shopId":"1","merchantId":"1","serialNumber":"BY-SJJNM-122","name":"Bomang Type 1","slugName":"bomang-type-a1","description":"huge truck","imageFiles":[{"imageName":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.jpg","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"}],"priceBefore":"200000000","price":"100000000","monthPrice":"29999","dayPrice":"830000","hourPrice":"188","startDateAvailable":"213123","endDateAvailable":"21321312","year":"2019","catalogId":"1","catalogName":"truck","locationId":"1002171","locationName":"Batam","conditionTypeId":"1","conditionTypeName":"New","stock":"1","rating":"3","viewCount":"38","lastSeen":"123123233","isQualified":"true","isAssurance":"true","latitude":"-6.30064100","longitude":"106.81409500","createdAt":"1683601805","updatedAt":"1683601805","technicalDetail":[{"label":"Opt Weight","value":"9 ton"},{"label":"Capacity","value":"80 ton"}],"reviews":{"total":10,"withImages":5,"customerSatisfied":[{"label":"Engine Perfomance","count":1},{"label":"Delivery Accuracy","count":1}],"customerUnstatisfied":[{"label":"Operator Perfomance","count":1},{"label":"Ability Accuracy","count":1}],"ratingProgress":{"0":10,"1":0,"2":0,"3":0,"4":0,"5":0}}}}');
    }

    public function getSpecification(string $xid, int $type)
    {
//        $response = Request::route('integration.scanina.product.specification.browse', $this->client)
//            ->queryParams([
//                'sellTypeId' => $type
//            ])
//            ->pathParams([
//                'xid' => $xid,
//            ])
//            ->send();
//
//        return $response->json();

        return json_decode('{"success":true,"code":"200","message":"OK","data":{"rows":[{"id":12,"name":"fuel","specificationColumn":[{"id":7,"name":"combust","value":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas nibh turpis, rhoncus nec enim sed, egestas lobortis ante."},{"id":8,"name":"electric","value":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas nibh turpis, rhoncus nec enim sed,"}],"subSpecification":[]},{"id":13,"name":"cabin","specificationColumn":[{"id":9,"name":"electric","value":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas nibh turpis, rhoncus nec enim sed,"}],"subSpecification":[]},{"id":14,"name":"engine","specificationColumn":[{"id":10,"name":"machine v8","value":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas nibh turpis, rhoncus nec enim sed,"}],"subSpecification":[]}]}}');
    }

    public function readSparePart(string $xid)
    {
//        $response = Request::route('integration.scanina.product.spare-part.read', $this->client)
//            ->pathParams([
//                'xid' => $xid,
//            ])
//            ->send();
//
//        return $response->json();

        return json_decode('{"success":true,"code":"200","message":"OK","data":{"xid":"HNS1231","shopId":"1","merchantId":"1","name":"diital-hud","nameSlug":"digital-hud","description":"lorem ipsum","imageFiles":[{"imageName":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.jpg","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"},{"imageName":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.jpg","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"}],"videoFile":{"name":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.mp4","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"},"documentationFile":{"name":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.pdf","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"},"priceBefore":"200000000","price":"100000000","year":"2019","catalogId":"1","catalogName":"digital","itemNumber":"33423232","weight":"1.3","length":"15.0","width":"35.0","height":"5.0","stock":"1","rating":"3","reviewCount":"10","itemSoldCount":"12","viewCount":"38","lastSeen":"123123233","createdAt":"1683601805","updatedAt":"1683601805","reviews":{"total":10,"withImages":5,"customerSatisfied":[{"label":"product Perfomance","count":1},{"label":"Delivery Accuracy","count":1}],"customerUnstatisfied":[{"label":"Operator Perfomance","count":1},{"label":"Ability Accuracy","count":1}],"ratingProgress":{"0":10,"1":0,"2":0,"3":0,"4":0,"5":0}}}}');
    }

    public function readService(string $xid)
    {
//        $response = Request::route('integration.scanina.product.service.read', $this->client)
//            ->pathParams([
//                'xid' => $xid,
//            ])
//            ->send();
//
//        return $response->json();

        return json_decode('{"success":true,"code":"200","message":"OK","data":{"xid":"HNS1231","shopId":"1","merchantId":"1","name":"Maintenance 300","nameSlug":"maintenance-300","description":"lorem ipsum","imageFiles":[{"imageName":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.jpg","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"},{"imageName":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.jpg","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"}],"videoFile":{"name":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.mp4","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"},"documentationFile":{"name":"HzZxHWtZO0e16H5tLABQpuGr27dCrGAj3GFKL8jS.pdf","path":"https://minio.nbs.co.id/dev-scan-web-bucket/product/ocXutXa43HfvkdmI6oH5RB6U5jMUavxTs58dJ8l9.jpg?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=usr_scan_web%2F20230509%2F%2Fs3%2Faws4_request&X-Amz-Date=20230509T030458Z&X-Amz-SignedHeaders=host&X-Amz-Expires=1800&X-Amz-Signature=c15ea2e2291b8c0ca53880f71b69d6cecbb1bf566f85212b2d201532fe2ac139"},"priceBefore":"200000000","price":"100000000","year":"2019","catalogId":"1","catalogName":"maintenance","stock":"1","rating":"3","reviewCount":"10","itemSoldCount":"12","viewCount":"38","lastSeen":"123123233","createdAt":"1683601805","updatedAt":"1683601805","reviews":{"total":10,"withImages":5,"customerSatisfied":[{"label":"Engine Perfomance","count":1},{"label":"Delivery Accuracy","count":1}],"customerUnstatisfied":[{"label":"Operator Perfomance","count":1},{"label":"Ability Accuracy","count":1}],"ratingProgress":{"0":10,"1":0,"2":0,"3":0,"4":0,"5":0}}}}');
    }

    public function getCustomerReview(string $xid, int $type)
    {
//        $response = Request::route('integration.scanina.product.customer-review.browse', $this->client)
//            ->queryParams([
//                'sellTypeId' => $type
//            ])
//            ->pathParams([
//                'xid' => $xid,
//            ])
//            ->send();
//
//        return $response->json();

        return json_decode('{"success":true,"code":"200","message":"OK","data":{"rows":[{"userData":{"id":28,"userSnapshot":{"fullName":"PT Maju Aja","id":9,"location":"Jawa Barat, Indonesia"},"images":[{"image":"test1.png","path":"https://minio.test/test1.png"},{"image":"test2.png","path":"https://minio.test/test2.png"}],"statisfiedWith":["Pengiriman","Harga","Kualitas Produk"],"unsatisfiedWith":["Pelayanan Penjual","Kemasan Produk"],"rating":5,"isCommentHidden":false,"isAnonymous":false,"createdAt":1617676832,"updatedAt":1617676963}}],"metadata":{"total":2,"count":2,"skip":0,"limit":10,"sort_by":"latest"}}}');
    }

    public function getFilterCategory(ScaninaProductFilterDto $arguments)
    {
//        $response = Request::route("integration.scanina.product.{$arguments->type}.category.browse", $this->client)
//            ->queryParams($arguments->toArray())
//            ->send();
//
//        return $response->json();

        return json_decode('{"success":true,"code":"200","message":"OK","data":{"rows":[{"id":"1","parent_id":"1","level":"1","name":"Wheel Loader","slugName":"wheel-loader","isRentActive":"false","topCategory":"false","unitTypeId":"1","unitTypeName":"Kilo meter","isActive":"true","createdAt":"1683601805","updatedAt":"1683601805"}],"metadata":{"total":1,"count":1,"skip":0,"limit":10,"sort_by":"latest"}}}');
    }

    public function getFilterBrand(ScaninaProductFilterDto $arguments)
    {
//        $response = Request::route("integration.scanina.product.{$arguments->type}.brand.browse", $this->client)
//            ->queryParams($arguments->toArray())
//            ->send();
//
//        return $response->json();

        return json_decode('{"success": true, "code": "200", "message": "OK", "data": {"rows": [{"id": "1", "name": "Catterpillar", "isActive": "true", "createdAt": "1683601805", "updatedAt": "1683601805"}], "metadata": {"total": 1, "count": 1, "skip": 0, "limit": 10, "sort_by": "latest"}}}');
    }

    public function getFilterType(ScaninaProductFilterDto $arguments)
    {
//        $response = Request::route("integration.scanina.product.{$arguments->type}.type.browse", $this->client)
//            ->queryParams($arguments->toArray())
//            ->send();
//
//        return $response->json();

        return json_decode('{"success": true, "code": "200", "message": "OK", "data": {"rows": [{"id": "1", "brandId": "1", "name": "type 1", "isActive": "true", "createdAt": "1683601805", "updatedAt": "1683601805"}], "metadata": {"total": 1, "count": 1, "skip": 0, "limit": 10, "sort_by": "latest"}}}');
    }

    public function getFilterModel(ScaninaProductFilterDto $arguments)
    {
//        $response = Request::route("integration.scanina.product.{$arguments->type}.model.browse", $this->client)
//            ->queryParams($arguments->toArray())
//            ->send();
//
//        return $response->json();

        return json_decode('{"success": true, "code": "200", "message": "OK", "data": {"rows": [{"id": "1", "typeId": "1", "name": "A1", "isActive": "true", "createdAt": "1683601805", "updatedAt": "1683601805"}], "metadata": {"total": 1, "count": 1, "skip": 0, "limit": 10, "sort_by": "latest"}}}');
    }

    public function getAccount(string $email)
    {
//        $response = Request::route("integration.scanina.user.account.check", $this->client)
//            ->json(['email' => $email])
//            ->send();
//
//        return $response->json();

        return json_decode('{"success":true,"code":"200","message":"OK","data":{"isRegistred":"true","user":{"email":"test@mail.com","fullName":"test user","typeId":"1","typeIdName":"personal","emailVerifiedAt":"1683601805","createdAt":"1683601805","updatedAt":"t1683601805"}}}');
    }

    public function register(ScaninaUserRegisterRequestDto $dto)
    {
//        $response = Request::route("integration.scanina.user.account.register", $this->client)
//            ->json($dto->toArray())
//            ->send();
//
//        return $response->json();

        return json_decode('{"success":true,"code":"200","message":"OK","data":{"isRegistred":"true","user":{"email":"test@mail.com","fullName":"test user","typeId":"1","typeIdName":"personal","emailVerifiedAt":"1683601805","createdAt":"1683601805","updatedAt":"t1683601805"}}}');
    }

    public function addToCart(AddToCartRequestDto $dto)
    {
//        $response = Request::route("integration.scanina.user.account.register", $this->client)
//            ->json($dto->toArray())
//            ->send();
//
//        return $response->json();

        return json_decode('{"success": true, "code": "200", "message": "OK", "data": {"productId": "1", "proudctName": "Catterpilar Bomang 2", "type": 1}}');
    }

    public function resendEmail(string $email)
    {
//        $response = Request::route("integration.scanina.user.account.check", $this->client)
//            ->json(['email' => $email])
//            ->send();
//
//        return $response->json();

        return json_decode('{"success": true, "code": "200", "message": "OK"}');
    }
}
