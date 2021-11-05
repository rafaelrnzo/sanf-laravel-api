<?php


namespace Sanf\Integration;


use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use stdClass;

class InternalApiClient
{
    /**
     * @param $email
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function findCustomerByEmail($email)
    {
        $response = Request::route('customer.find-by-email')
            ->pathParams(['email' => $email])
            ->send();
        return $response->json();
    }

    /**
     * @param $id
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function findCustomerById($id)
    {
        $response = Request::route('customer.find-by-id')
            ->pathParams(['id' => $id])
            ->send();
        return $response->json();
    }

    /**
     * @param $email
     * @param $npwp
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
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

    /**
     * @param $name
     * @param $email
     * @param $landlineNumber
     * @param $phoneNumber
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
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

    /**
     * @param $data
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
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

    /**
     * @param $data
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
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

    /**
     * @param $id
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getShareholders($id)
    {
        $response = Request::route('customer.shareholder.list')
            ->pathParams(['id' => $id])
            ->send();

        return $response->json();
    }

    /**
     * @param $request
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
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

    /**
     * @param $request
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
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

    /**
     * @param $id
     * @param $no
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
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

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getProvinces()
    {
        $response = Request::route('location.provinces')->send();

        return $response->json();
    }

    /**
     * @param $province_id
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getCities($province_id)
    {
        $response = Request::route('location.cities')
            ->pathParams(['province_id' => $province_id])
            ->send();

        return $response->json();
    }

    /**
     * @param $province_id
     * @param $city_id
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getDistrict($province_id, $city_id)
    {
        $response = Request::route('location.districts')
            ->pathParams([
                'province_id' => $province_id,
                'city_id' => $city_id,
            ])->send();

        return $response->json();
    }

    /**
     * @param $province_id
     * @param $city_id
     * @param $district_name
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
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

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getPosition()
    {
        $response = Request::route('customer.positions')->send();

        return $response->json();
    }

    /**
     * @param $type
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
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

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getBrands()
    {
        $response = Request::route('financing-object.brand')->send();

        return $response->json();
    }

    /**
     * @param $brandId
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getTypes($brandId)
    {
        $response = Request::route('financing-object.type')
            ->pathParams(['brand_id' => $brandId])
            ->send();

        return $response->json();
    }

    /**
     * @param $brandId
     * @param $typeId
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getModels($brandId, $typeId)
    {
        $response = Request::route('financing-object.model')
            ->pathParams([
                'brand_id' => $brandId,
                'type_id' => $typeId,
            ])->send();

        return $response->json();
    }

    /**
     * @param $userId
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function validateKtp($userId)
    {
        $response = Request::route('financing-completion.ktp')
            ->pathParams([
                'user_id' => $userId,
            ])->send();

        return $response->json();
    }

    /**
     * @param $customerId
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function validateNpwp($customerId)
    {
        $response = Request::route('financing-completion.npwp')
            ->pathParams([
                'customer_id' => $customerId,
            ])->send();

        return $response->json();
    }

    /**
     * @param $request
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function uploadFinancingAsset($request)
    {
        $response = Request::route('customer.upload')
            ->json([
                'image' => $request->file,
                'author' => $request->author,
                'JenisDoc' => $request->asset_type,
                'CustomerId' => $request->xid,
            ])
            ->send();

        return $response->json();
    }

    /**
     * @param $customerId
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     * {
     * 'P_CODE': '001',
     * 'JENIS_PLAFOND': 'UNIT',
     * 'CUST_ID': '3010000050',
     * 'P_CURRENT': '0',
     * 'P_USED': '0',
     * 'P_SISA': '0',
     * 'DATE_UPDATE': '03-NOV-21'
     * }
     */
    public function getCustomerPlafonds($customerId){
        $response = Request::route('customer.plafond.list')
            ->pathParams([
                'customer_id' => $customerId,
            ])->send();
        return $response->json();
    }

    /**
     * @param $customerId
     * @param $plafondCode
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     * {
     * 'header': [
     * {
     * 'P_CODE': '001',
     * 'JENIS_PLAFOND': 'UNIT',
     * 'CUST_ID': '3010000050',
     * 'P_CURRENT': '0',
     * 'P_USED': '0',
     * 'P_SISA': '0',
     * 'DATE_UPDATE': '03-NOV-21'
     * }
     * ],
     * 'items': [
     * {
     * 'P_CODE': '001',
     * 'PLAFONDHEADER_ID': 'PH5',
     * 'CUST_ID': '3010000050',
     * 'P_SUBMIT': '832000000',
     * 'P_CURRENT': '0',
     * 'P_TAMBAHAN': '832000000',
     * 'P_STATUS': '1',
     * 'DESCRIPTION': 'IN PROGRESS',
     * 'DATE_UPDATE': '29-OCT-21 11.33.24.000000 AM'
     * }
     * ]
     * }
     */
    public function getCustomerPlafondsByType($customerId, $plafondCode){
        $response = Request::route('customer.plafond.list-by-type')
            ->pathParams([
                'customer_id' => $customerId,
                'p_code' => $plafondCode,
            ])->send();
        return $response->json();
    }

    /**
     * @param $customerId
     * @param $typeId 001,002
     * @param $amount
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function requestPlafond($customerId, $plafondCode, $amount){
        $response = Request::route('customer.plafond.create')
            ->json([
                'cust_id' => $customerId,
                'p_code' => $plafondCode,
                'amount' => $amount,
            ])
            ->send();
        return $response->json();
    }
}
