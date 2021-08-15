<?php


namespace Sanf\Api\Modules\User;


use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestController;
use Sanf\Core\Modules\User\RegisterAsContractOwnerService;

class ProfileController extends RestController
{
    public function getList(Request $request)
    {
        $result = [
            [
            "xid" => "4010000127",
            "type_id" => "P",
            "type_name" => "PERSONAL",
            "title" => "MR.",
            "full_name" => "ARNES",
            "pic_name" => "arnes gosal",
            "identity_number" => "7306080805820001",
            "npwp" => "143159416804000",
            "email" => "arnes.gosal@yahoo.co.id",
            "landline_number" => "08152511191",
            "phone_number" => "081355878052",
            "gender" => "M",
            "birthdate" => "1993-12-01",
            "country_id" => "001",
            "country_name" => "INDONESIA",
            "province_id" => "00103",
            "province_name" => "JAKARTA",
            "city_id" => "0010394",
            "city_name" => "JAKARTA SELATAN",
            "district_name" => "PASAR MINGGU",
            "subdistrict_name" => "RAGUNAN",
            "postcode" => "12550",
            "address" => "JL. DG. TATA LR.3 NO.11E RT 001 RW 007",
            "business_since" => "2008",
            "is_active" => true,
            "is_pic" => true
        ], [
            "xid" => "2010000310",
            "type_id" => "C",
            "type_name" => "COMPANY",
            "title" => "PT",
            "full_name" => "PT TANJUNG BUYU PERKASA PLANTATION",
            "pic_name" => "AHMAD GUNUNG",
            "identity_number" => "123456576283768761232",
            "npwp" => "012161758038000",
            "email" => "rossannalie@gmail.com",
            "landline_number" => "021-5600977-78",
            "phone_number" => "081234567849",
            "gender" => "F",
            "birthdate" => "1993-12-01",
            "country_id" => "001",
            "country_name" => "INDONESIA",
            "province_id" => "00103",
            "province_name" => "JAKARTA",
            "city_id" => "0010394",
            "city_name" => "JAKARTA SELATAN",
            "district_name" => "PASAR MINGGU",
            "subdistrict_name" => "RAGUNAN",
            "postcode" => "12550",
            "address" => "BELTWAY OFFICE PARK TOWER B, LANTAI 7",
            "business_since" => "0",
            "is_active" => true,
            "is_pic" => true,
        ]
        ];
        $result = json_decode(json_encode($result));
        return fractal($result, CustomerProfileTransformer::class);
    }

    public function getDetail($xid)
    {
        $result = [
            "xid" => "4010000127",
            "type_id" => "P",
            "type_name" => "PERSONAL",
            "title" => "MR.",
            "full_name" => "ARNES",
            "pic_name" => "arnes gosal",
            "identity_number" => "7306080805820001",
            "npwp" => "143159416804000",
            "email" => "arnes.gosal@yahoo.co.id",
            "landline_number" => "08152511191",
            "phone_number" => "081355878052",
            "gender" => "M",
            "birthdate" => "1993-12-01",
            "country_id" => "001",
            "country_name" => "INDONESIA",
            "province_id" => "00103",
            "province_name" => "JAKARTA",
            "city_id" => "0010394",
            "city_name" => "JAKARTA SELATAN",
            "district_name" => "PASAR MINGGU",
            "subdistrict_name" => "RAGUNAN",
            "postcode" => "12550",
            "address" => "JL. DG. TATA LR.3 NO.11E RT 001 RW 007",
            "business_since" => "2008",
            "is_active" => true,
            "is_pic" => true,
        ];
        $result = json_decode(json_encode($result));
        return fractal($result, CustomerProfileTransformer::class);
    }

    public function postCreateCompanyProfile(Request $request)
    {
        $this->validate($request, [
            "title" => ["required", "string"],
            "full_name" => ["required", "string"],
            "npwp" => ["required", "string"],
            "landline_number" => ["nullable", "string"],
            "pic_name" => ["required", "string"],
            "phone_number" => ["required", "string"],
            "email" => ["required", "string"]
        ]);
        return $this->responseOk();
    }

    public function putUpdateCompanyProfile(Request $request)
    {
        $this->validate($request, [
            "email" => ["required", "string"],
        ]);
        return $this->responseOk();
    }

    public function putUpdatePersonalProfile(Request $request)
    {
        $this->validate($request, [
            "email" => ["required", "string"],
        ]);
        return $this->responseOk();
    }

    public function postRegisterWithContract(Request $request, RegisterAsContractOwnerService $service)
    {
        $validated = $this->validate($request, [
            "email" => ["required", "string"],
        ]);
        $dto = (object)[
            'email' => $validated['email']
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    public function postSwitch($xid)
    {
        $this->responseOk();
    }
}
