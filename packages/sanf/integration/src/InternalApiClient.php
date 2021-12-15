<?php


namespace Sanf\Integration;


use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Core\Modules\Contract\Dto\AccountReceivableContractDto;
use Sanf\Core\Modules\Contract\Dto\ListContractDto;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use stdClass;

class InternalApiClient
{
    const DEFAULT_SKIP = 0;
    const DEFAULT_LIMIT = 10;
    const DEFAULT_ORDER = 'Earliest';

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
             * {
             * "cust_accnt": "3ACCNT",
             * "cust_title": "PT",
             * "nama": "SURYA ABADI 4",
             * "npwp": "01281821882883817",
             * "no_telp": "0218199998",
             * "pic_name": "Rizma Utami",
             * "no_hp": "0819291991923",
             * "email": "rizmautami@sanf.co.id"
             * }
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
         * {
         * "CUST_ID": "2010000334",
         * "SR_NO": "1",
         * "CUST_TITLE": "MRS",
         * "CUST_NAME": "LUCYNDA TANJUNG",
         * "PERC_SHARE": "0",
         * "JABATAN": "DIREKTUR",
         * "F_PC": "P",
         * "EMAIL": "LUCYNDA_TANJUNG@GMAIL.COM"
         * }
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
     * @param $customerId
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function validateKtp($customerId)
    {
        $response = Request::route('financing-completion.ktp')
            ->pathParams([
                'customer_id' => $customerId,
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
    public function getCustomerPlafonds($customerId)
    {
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
    public function getCustomerPlafondsByType($customerId, $plafondCode)
    {
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
    public function requestPlafond($customerId, $plafondCode, $amount)
    {
        $response = Request::route('customer.plafond.create')
            ->json([
                'cust_id' => $customerId,
                'p_code' => $plafondCode,
                'amount' => $amount,
            ])
            ->send();
        return $response->json();
    }

    /**
     * @param $customerId
     * @param string $plafondCode
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     * {
     * 'status': true,
     * 'code': 'S_GetData',
     * 'message': 'Success',
     * 'count': 6,
     * 'data': [
     * {
     * 'P_CODE': '001',
     * 'PLAFONDHEADER_ID': 'PH2',
     * 'CUST_ID': '2010000138',
     * 'P_SUBMIT': '744040000',
     * 'P_CURRENT': '0',
     * 'P_TAMBAHAN': '744040000',
     * 'P_STATUS': '4',
     * 'DESCRIPTION': 'CLOSED',
     * 'DATE_UPDATE': '17-MAR-07 12.00.00.000000 AM'
     * },
     * {
     * 'P_CODE': '001',
     * 'PLAFONDHEADER_ID': 'PH2',
     * 'CUST_ID': '2010000138',
     * 'P_SUBMIT': '2185128000',
     * 'P_CURRENT': '0',
     * 'P_TAMBAHAN': '2185128000',
     * 'P_STATUS': '4',
     * 'DESCRIPTION': 'CLOSED',
     * 'DATE_UPDATE': '26-JUN-07 12.00.00.000000 AM'
     * }
     * ]
     * }
     */
    public function getCustomerPlafondHistories($customerId, $plafondCode = 'all')
    {
        $response = Request::route('customer.plafond.history')
            ->queryParams([
                'uid' => $customerId,
                'typeplafond' => $plafondCode
            ])
            ->send();
        return $response->json();
    }

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getMetadataContract($user_id)
    {
        $response = Request::route('contract.metadata')
            ->queryParams(['cust_id' => $user_id])
            ->send();

        return $response->json();
    }

    /**
     * @param AccountReceivableContractDto $dto
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getAccountReceivable(AccountReceivableContractDto $dto)
    {
        $response = Request::route('contract.account-receivable')
            ->queryParams([
                'cust_id' => $dto->user_id,
                'curr' => $dto->currency_type,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'order' => $dto->sort_by,
            ])
            ->send();

        return $response->json();
    }

    /**
     * @param ListContractDto $dto
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getContractList(ListContractDto $dto)
    {
        $response = Request::route('contracts')
            ->queryParams([
                'cust_id' => $dto->user_id,
                'status' => $dto->contract_type,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'order' => $dto->sort_by,
            ])
            ->send();

        return $response->json();
    }

    /**
     * @param $customerId
     * @param int $offset
     * @param int $limit
     * @param string $order
     * @param int|null $timestamp
     * @param string|null $keyword
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     *{
     * 'status': true,
     * 'code': 'S_GetData',
     * 'message': 'Success',
     * 'count': 10,
     * 'data': [
     * {
     * 'AGREE_NO': '30707000086',
     * 'PAY_AMT': '78341.44',
     * 'CURR_ID': 'USD',
     * 'ROWINDEX': '1'
     * }
     * ]
     * }
     */
    public function getContractOfPrepayment($customerId, $skip, $limit, $order, ?int $timestamp, ?string $keyword)
    {
        $response = Request::route('prepayment.contract.list')
            ->queryParams([
                'cust_id' => $customerId,
                'skip' => $skip ?? self::DEFAULT_SKIP,
                'limit' => $limit ?? self::DEFAULT_LIMIT,
                'order' => $order ?? self::DEFAULT_ORDER,
                'timestamp' => $timestamp,
                'keyword' => $keyword
            ])
            ->send();
        return $response->json(false);
    }

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     * {
     * 'status': true,
     * 'code': 'S_GetData',
     * 'message': 'Success',
     * 'count': 7,
     * 'data': {
     * 'NO_KONTRAK': '30712000741',
     * 'TGL_PREPAY': '31102008',
     * 'TOTAL_PAYMENT': '1475000000',
     * 'ITEM': [
     * {
     * 'DESCRIPTION': 'Outstanding Principal',
     * 'JUMLAH': '1302649294.02'
     * },
     * {
     * 'DESCRIPTION': 'Installment Overdue',
     * 'JUMLAH': '107974000'
     * },
     * {
     * 'DESCRIPTION': 'Prepayment Penalty',
     * 'JUMLAH': '32566232'
     * },
     * {
     * 'DESCRIPTION': 'Advance Payment Customer',
     * 'JUMLAH': '0'
     * },
     * {
     * 'DESCRIPTION': 'Admin Charge Prepay',
     * 'JUMLAH': '500000'
     * },
     * {
     * 'DESCRIPTION': 'Overdue Penalty',
     * 'JUMLAH': '15856557.98'
     * },
     * {
     * 'DESCRIPTION': 'Bunga Berjalan Prepay',
     * 'JUMLAH': '15453916'
     * }
     * ]
     * }
     * }
     */
    public function getPrepaymentDetail(string $contractNo, \DateTimeImmutable $prepaymentDate)
    {
        $response = Request::route('customer.plafond.history')
            ->queryParams([
                'AgreeNo' => $contractNo,
                'TglPrepay' => Carbon::createFromImmutable($prepaymentDate)->format('dmY')
            ])
            ->send();
        return $response->json();
    }
}
