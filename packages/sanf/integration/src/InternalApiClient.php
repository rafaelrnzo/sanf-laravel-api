<?php


namespace Sanf\Integration;


use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use stdClass;

class InternalApiClient
{
    public function findCustomerByEmail($email)
    {
        $response = Request::route('customer.find-by-email')
            ->pathParams(['email' => $email])
            ->send();
        return $response->json();
    }

    public function findCustomerById($id)
    {
        $response = Request::route('customer.find-by-id')
            ->pathParams(['id' => $id])
            ->send();
        return $response->json();
    }

    public function findByEmailAndNpwp($email, $npwp)
    {
        $response = Request::route('customer.find-by-email-and-npwp')
            ->pathParams([
                'email' => $email,
                'npwp' => $npwp
            ])
            ->send();
        return $response->json();
    }

    public function registerPersonal($name, $email, $landlineNumber, $phoneNumber)
    {
        $response = Request::route('customer.register')
            ->json([
                'nama' => $name,
                'email' => $email,
                'notelp' => $landlineNumber,
                'nohp' => $phoneNumber,
            ])
            ->send();
        return $response->json();
    }


    public function createCompany($data) //TODO DTO
    {
        $response = Request::route('customer.create-company')
            /**
            {
            "cust_accnt": "3ACCNT",
            "cust_title": "PT",
            "nama": "SURYA ABADI 4",
            "npwp": "01281821882883817",
            "no_telp": "0218199998",
            "pic_name": "Rizma Utami",
            "no_hp": "0819291991923",
            "email": "rizmautami@sanf.co.id"
            }
             */
            ->json($data)
            ->send();
        return $response->json();
    }

    public function updateCustomer($data)//TODO DTO
    {
        $response = Request::route('customer.update')
            /*
            {
              "cust_id": "2010002519",
              "cust_type": "P",
              "cust_title": "MR",
              "nama": "JAMOMBANG O SUNGGU",
              "picname": "BUDI",
              "ktp": "3172032806570002",
              "npwp": "174263178045000",
              "email": "febrian.alexandro@gmail.com",
              "notelp": "08128076703",
              "nohp": "08128076703",
              "gender": "M",
              "tgl_lahir": "1957/06/28",
              "idprov": "00103",
              "prov": "JAKARTA",
              "idkota": "0010392",
              "kota": "JAKARTA UTARA",
              "kecamatan": "KOJA",
              "kelurahan": "TUGU SELATAN",
              "kodepos": "14260"
            }
          */
            ->json($data)
            ->send();
        return $response->json();
    }

    public function getShareholders($id)
    {
        $response = Request::route('customer.shareholder.list')
            ->pathParams(['id' => $id])
            ->send();

        return $response->json();
    }

    public function createShareholder($request) //TODO USE DTO
    {
        $response = Request::route('customer.shareholder.create')
            ->json([
                "cust_id" => $request->id,
                "cust_title" => $request->title,
                "cust_name" => $request->name,
                "job_desc" => $request->job,
                "percshare" => $request->percentage,
                "type" => $request->type
            ])
            ->send();

        return $response->json();
    }

    public function updateShareholder($request)
    {
        $response = Request::route('customer.shareholder.update')
            ->json([
                "cust_id" => $request->id,
                "sr_no" => $request->no,
                "cust_title" => $request->title,
                "cust_name" => $request->name,
                "job_desc" => $request->job,
                "percshare" => $request->percentage,
                "type" => $request->type,

            ])
            ->send();

        return $response->json();
    }

    public function deleteShareholder($id, $no)
    {
        $response = Request::route('customer.shareholder.delete')
            ->json([
                "cust_id" => $id,
                "sr_no" => $no,
            ])
            ->send();

        return $response->json();
    }

    public function getProvinces()
    {
        $response = Request::route('location.provinces')->send();

        return $response->json();
    }

    public function getCities($province_id)
    {
        $response = Request::route('location.cities')
            ->pathParams(['province_id' => $province_id])
            ->send();

        return $response->json();
    }

    public function getDistrict($province_id, $city_id)
    {
        $response = Request::route('location.districts')
            ->pathParams([
                'province_id' => $province_id,
                'city_id' => $city_id,
            ])->send();

        return $response->json();
    }

    public function getSubDistrict($province_id, $city_id, $district_name)
    {
        $response = Request::route('location.sub-districts')
            ->pathParams([
                'province_id' => $province_id,
                'city_id' => $city_id,
                'district_id' => $district_name,
            ])->send();

        return $response->json();
    }

    public function getPosition()
    {
        $response = Request::route('customer.positions')->send();

        return $response->json();
    }

    public function getTitle($type)
    {
        $response = Request::route('customer.titles')
            ->pathParams([
                'type' => $type,
            ])->send();

        return $response->json();
    }

    /**
     * @param $id
     * @return array|stdClass|null
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     * @throws SanfInternalApiDataNotFoundException
     */
    public function getStaffs($id)
    {
        /**
        {
        "CUST_ID": "2010000334",
        "SR_NO": "1",
        "CUST_TITLE": "MRS",
        "CUST_NAME": "LUCYNDA TANJUNG",
        "PERC_SHARE": "0",
        "JABATAN": "DIREKTUR",
        "F_PC": "P",
        "EMAIL": "LUCYNDA_TANJUNG@GMAIL.COM"
        }
         */
        $response = Request::route('customer.staff.list')
            ->pathParams(['id' => $id])
            ->send();

        return $response->json();
    }

    public function getBrands()
    {
        $response = Request::route('financing-object.brand')->send();

        return $response->json();
    }

    public function getTypes($brandId)
    {
        $response = Request::route('financing-object.type')
            ->pathParams(['brand_id' => $brandId])
            ->send();

        return $response->json();
    }

    public function getModels($brandId, $typeId)
    {
        $response = Request::route('financing-object.model')
            ->pathParams([
                'brand_id' => $brandId,
                'type_id' => $typeId,
            ])->send();

        return $response->json();
    }
}
