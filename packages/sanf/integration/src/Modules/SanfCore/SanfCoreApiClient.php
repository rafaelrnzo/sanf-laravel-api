<?php

namespace Sanf\Integration\Modules\SanfCore;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Core\Modules\Plafond\Dtos\PlafondDisbursementCoreFormRequest;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;

class SanfCoreApiClient
{
    public const DEFAULT_SKIP = 0;
    public const DEFAULT_LIMIT = 2147483647;
    public const DEFAULT_ORDER = 'Latest';

    protected $client;

    public function __construct()
    {
        $verifyOnProduction = config('app.env') === 'production';

        $this->client = new Client([
            'verify' => $verifyOnProduction,
        ]);
    }

    /**
     * @param $email
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function findCustomerByEmail($email)
    {
        /**
         * {
         * 'CUST_ID_SANF': '2010000125',
         * 'ID_IDENTITY': 'C',
         * 'DESC_IDENTITY': 'COMPANY',
         * 'COMPANY_TYPE': 'PT.',
         * 'IDENTITY_NAME': 'PT. MITRA INDAH LESTARI',
         * 'PIC_NAME': 'ADE S',
         * 'KTP': null,
         * 'NPWP': '024411811725000',
         * 'EMAIL_ADDR': 'ade.s@nusantarabetastudio.com',
         * 'NO_TELP': '0542748657',
         * 'NO_HP': '0811542682',
         * 'GENDER': ' ',
         * 'TGL_LAHIR': '1970/01/01',
         * 'ID_NEGARA': '001',
         * 'ID_PROVINSI': '00154',
         * 'PROVINSI': 'KALIMANTAN TIMUR',
         * 'ID_KOTA': '0015492',
         * 'KOTA': 'BALIKPAPAN',
         * 'KECAMATAN': 'BALIKPAPAN UTARA',
         * 'KELURAHAN': 'BATU AMPAR',
         * 'KODEPOS': '76126',
         * 'ALAMAT': 'JL.SOEKARNO HATTA RT.005 KEL.BATU AMPAR',
         * 'LAMA_USAHA': '2005',
         * 'F_ACTIVE': 'Y',
         * 'PIC': '1',
         * 'NO_AE': null
         * }.
         */
        $response = Request::route('customer.find-by-email', $this->client)
            ->json(['email' => $email])
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
        /**
         * {
         * 'CUST_ID_SANF': '2010000125',
         * 'ID_IDENTITY': 'C',
         * 'DESC_IDENTITY': 'COMPANY',
         * 'COMPANY_TYPE': 'PT.',
         * 'IDENTITY_NAME': 'PT. MITRA INDAH LESTARI',
         * 'PIC_NAME': 'ADE S',
         * 'KTP': null,
         * 'NPWP': '024411811725000',
         * 'EMAIL_ADDR': 'ade.s@nusantarabetastudio.com',
         * 'NO_TELP': '0542748657',
         * 'NO_HP': '0811542682',
         * 'GENDER': ' ',
         * 'TGL_LAHIR': '1970/01/01',
         * 'ID_NEGARA': '001',
         * 'NEGARA': 'INDONESIA',
         * 'ID_PROVINSI': '00154',
         * 'PROVINSI': 'KALIMANTAN TIMUR',
         * 'ID_KOTA': '0015492',
         * 'KOTA': 'BALIKPAPAN',
         * 'KECAMATAN': 'BALIKPAPAN UTARA',
         * 'KELURAHAN': 'BATU AMPAR',
         * 'KODEPOS': '76126',
         * 'ALAMAT': 'JL.SOEKARNO HATTA RT.005 KEL.BATU AMPAR',
         * 'LAMA_USAHA': '2005',
         * 'F_ACTIVE': 'Y',
         * 'PIC': '1',
         * 'NO_AE': null,
         * 'EMAIL_STAFF': 'suhendar.ade23@gmail.com'
         * }.
         */
        $response = Request::route('customer.find-by-id', $this->client)
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
        $response = Request::route('customer.find-by-email-and-npwp', $this->client)
            ->pathParams([
                'email' => $email,
                'npwp' => $npwp,
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
    public function registerPersonal($name, $email, $landlineNumber = null, $phoneNumber = null)
    {
        $response = Request::route('customer.register', $this->client)
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
        $response = Request::route('customer.create-company', $this->client)
            /*
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
    public function updateCustomer($data) //TODO DTO
    {
        $response = Request::route('customer.update', $this->client)
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
        $response = Request::route('customer.shareholder.list', $this->client)
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
        $response = Request::route('customer.shareholder.create', $this->client)
            ->json([
                'cust_id' => $request->id,
                'cust_title' => $request->title,
                'cust_name' => $request->name,
                'job_desc' => $request->job,
                'percshare' => $request->percentage,
                'type' => $request->type,
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
        $response = Request::route('customer.shareholder.update', $this->client)
            ->json([
                'cust_id' => $request->id,
                'sr_no' => $request->no,
                'cust_title' => $request->title,
                'cust_name' => $request->name,
                'job_desc' => $request->job,
                'percshare' => $request->percentage,
                'type' => $request->type,

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
        $response = Request::route('customer.shareholder.delete', $this->client)
            ->json([
                'cust_id' => $id,
                'sr_no' => $no,
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
        $response = Request::route('location.provinces', $this->client)->send();

        return $response->json();
    }

    /**
     * @param $province_id
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getCitiesByProviceId($province_id)
    {
        $response = Request::route('location.cities', $this->client)
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
        $response = Request::route('location.districts', $this->client)
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
        $response = Request::route('location.sub-districts', $this->client)
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
        $response = Request::route('customer.positions', $this->client)->send();

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
        $response = Request::route('customer.titles', $this->client)
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
         * }.
         */
        $response = Request::route('customer.staff.list', $this->client)
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
        $response = Request::route('financing-object.brand', $this->client)->send();

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
        $response = Request::route('financing-object.type', $this->client)
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
        $response = Request::route('financing-object.model', $this->client)
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
        $response = Request::route('financing-completion.ktp', $this->client)
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
        $response = Request::route('financing-completion.npwp', $this->client)
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
        $response = Request::route('customer.upload', $this->client)
            ->multipart([
                [
                    'name' => 'image',
                    'contents' => file_get_contents($request->file->getPathName()),
                    'filename' => $request->file->getClientOriginalName(),
                ],
                [
                    'name' => 'filename',
                    'contents' => $request->file->getClientOriginalName(),
                ],
                [
                    'name' => 'author',
                    'contents' => $request->author,
                ],
                [
                    'name' => 'JenisDoc',
                    'contents' => $request->asset_type,
                ],
                [
                    'name' => 'CustomerId',
                    'contents' => $request->xid,
                ],
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
     * 'P_CURRENT': '0', -> P_TOTAL
     * 'P_USED': '0', -> P_TERPAKAI
     * 'P_SISA': '0', -> P_SISA
     * 'DATE_UPDATE': '03-NOV-21'
     * }
     */
    public function getCustomerPlafonds($customerId)
    {
        $response = Request::route('plafond.list', $this->client)
            ->queryParams([
                'cust_id' => $customerId,
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
        $response = Request::route('plafond.list-by-type', $this->client)
            ->queryParams([
                'cust_id' => $customerId,
                'code_plafond' => $plafondCode,
                'skip' => 0,
                'limit' => 1000,
                'order' => 'Latest',
            ])->send();

        return $response->json();
    }

    /**
     * @param $customerId
     * @param $typeId 001,002,003
     * @param $amount
     * @param $notes
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function requestPlafond($customerId, $plafondCode, $code, $amount = 0, $plafondId = null, $notes = null)
    {
        $response = Request::route('plafond.create', $this->client)
            ->json([
                'cust_id' => $customerId,
                'p_code' => $plafondCode,
                't_code' => $code,
                'amount' => $amount,
                'noplafond' => $plafondId,
                'notes' => $notes,
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
        $response = Request::route('plafond.history', $this->client)
            ->queryParams([
                'cust_id' => $customerId,
                'typeplafond' => $plafondCode,
            ])
            ->send();

        return $response->json();
    }

    public function getPlafondFactoring($customerId)
    {
        $response = Request::route('plafond.factoring', $this->client)
            ->queryParams([
                'cust_id' => $customerId,
                'code_plafond' => PlafondTypeEnum::FACTORING,
                'skip' => self::DEFAULT_SKIP,
                'limit' => self::DEFAULT_LIMIT,
                'order' => self::DEFAULT_ORDER,
            ])
            ->send();

        return $response->json();
    }

    public function getPlafondFactoringV2($customerId, $plafondCode = PlafondTypeEnum::FACTORING)
    {
        $response = Request::route('v2.plafond.factoring', $this->client)
            ->queryParams([
                'skip' => self::DEFAULT_SKIP,
                'limit' => self::DEFAULT_LIMIT,
            ])
            ->pathParams([
                'cust_id' => $customerId,
                'plafond_code' => $plafondCode,
            ])
            ->send();

        return $response->json();
    }

    public function getPlafondBowheerV2($customerId, $bowheerCode)
    {
        $response = Request::route('v2.plafond.bowheer', $this->client)
            ->queryParams([
                'skip' => self::DEFAULT_SKIP,
                'limit' => self::DEFAULT_LIMIT,
            ])
            ->pathParams([
                'cust_id' => $customerId,
                'bowheer_code' => $bowheerCode,
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
        $response = Request::route('contract.metadata', $this->client)
            ->queryParams(['cust_id' => $user_id])
            ->send();

        return $response->json(false);
    }

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getAccountReceivable(
        $customerId,
        $currency_type,
        $limit,
        $skip,
        $sort_by
    ) {
        $response = Request::route('contract.account-receivable', $this->client)
            ->queryParams([
                'cust_id' => $customerId,
                'curr' => $currency_type,
                'skip' => $skip,
                'limit' => $limit,
                'order' => $sort_by,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getContractList(
        $user_id,
        $contract_type,
        $limit,
        $skip,
        $sort_by
    ) {
        $response = Request::route('contracts', $this->client)
            ->queryParams([
                'cust_id' => $user_id,
                'status' => $contract_type,
                'skip' => $skip,
                'limit' => $limit,
                'order' => $sort_by,
            ])
            ->send();

        return $response->json(false);
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
        $response = Request::route('prepayment.contract.list', $this->client)
            ->queryParams([
                'cust_id' => $customerId,
                'skip' => $skip ?? self::DEFAULT_SKIP,
                'limit' => $limit ?? self::DEFAULT_LIMIT,
                'order' => $order ?? self::DEFAULT_ORDER,
                'timestamp' => $timestamp,
                'no_kontrak' => $keyword,
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
     * 'CURR_ID': 'IDR',
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
        $response = Request::route('prepayment.detail', $this->client)
            ->queryParams([
                'AgreeNo' => $contractNo,
                'TglPrepay' => $prepaymentDate->format('dmY'),
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getContractDetail($user_id, $contract_no)
    {
        $response = Request::route('contracts.detail', $this->client)
            ->queryParams([
                'cust_id' => $user_id,
                'contrak_no' => $contract_no,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getFinancingUnitItem(
        $user_id,
        $contract_no,
        $limit,
        $skip,
        $sort_by
    ) {
        $response = Request::route('contracts.financing-unit.item', $this->client)
            ->queryParams([
                'cust_id' => $user_id,
                'contrak_no' => $contract_no,
                'skip' => $skip,
                'limit' => $limit,
                'order' => $sort_by,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getFinancingUnitInvoice(
        $user_id,
        $contract_no,
        $limit,
        $skip,
        $sort_by
    ) {
        $response = Request::route('contracts.financing-unit.invoice', $this->client)
            ->queryParams([
                'cust_id' => $user_id,
                'contrak_no' => $contract_no,
                'skip' => $skip,
                'limit' => $limit,
                'order' => $sort_by,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @deprecated CR2025 @see self::giroContractV2()
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     * {
     * 'status': true,
     * 'code': 'S_GetData',
     * 'message': 'Success',
     * 'total': 1,
     * 'count': 1,
     * 'data': [
     * {
     * 'AGREE_NO': '30710000420',
     * 'CURR_ID': 'IDR',
     * 'DT_GL': '31-10-2007',
     * 'ROWINDEX': '1'
     * }
     * ]
     * }
     */
    public function getPdc($customerId, $limit, $skip, $sort_by, $keyword = null)
    {
        $response = Request::route('contracts.pdc', $this->client)
            ->queryParams([
                'cust_id' => $customerId,
                'contrak_no' => $keyword,
                'skip' => $skip,
                'limit' => $limit,
                'order' => $sort_by,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @return \stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     * {
     * "status": true,
     * "code": "S_GetData",
     * "message": "Success",
     * "total": 8,
     * "count": 8,
     * "data": [
     * {
     * "AGREE_NO": "31902000228",
     * "CURR_ID": "IDR",
     * "DT_GL": "2019-03-29",
     * "PDC_DUE_DT": "2022-12-25",
     * "ROWINDEX": "1"
     * },
     * ]
     * }
     */
    public function getPdcContractV2($customerId, $limit, $skip, $sort_by, $keyword = null, $date_min = null, $date_max = null)
    {
        $response = Request::route('v2.contracts.pdc', $this->client)
            ->pathParams([
                'cust_id' => $customerId,
            ])
            ->queryParams([
                'agree_no' => $keyword,
                'skip' => $skip,
                'limit' => $limit,
                'order' => $sort_by,
                'date_min' => $date_min,
                'date_max' => $date_max,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @deprecated CR2025 @see self::getPdcGiroByContractV2()
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getPdcDetail(
        $customerId,
        $contractNo,
        $limit,
        $skip,
        $sortBy
    ) {
        $response = Request::route('contracts.pdc.detail', $this->client)
            ->queryParams([
                'cust_id' => $customerId,
                'contrak_no' => $contractNo,
                'skip' => $skip,
                'limit' => $limit,
                'order' => $sortBy,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @return \stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     * {
     * "status": true,
     * "code": "S_GetData",
     * "message": "Success",
     * "total": 74,
     * "count": 10,
     * "data": [
     * {
     * "CUST_ID": "2010000284",
     * "AGREE_NO": "10801000539",
     * "PDC_DUE_DT": "2009-05-16",
     * "PDC_NO": "BI861017",
     * "CURR_ID": "IDR",
     * "PDC_AMT": "164050000",
     * "PDC_TYPE": "Angsuran",
     * "STATUS_ID": "6",
     * "STATUS": "CAIR",
     * "ROWINDEX": "1"
     * }
     * ]
     * }
     */
    public function getPdcGiroByContractV2(
        $customerId,
        array $agree_no,
        $date_min,
        $date_max,
        $status_id,
        $limit,
        $skip,
        $sortBy
    ) {
        $response = Request::route('v2.contracts.pdc.detail', $this->client)
            ->pathParams([
                'cust_id' => $customerId,
            ])
            ->queryParams([
                'agree_no' => $agree_no,
                'date_min' => $date_min,
                'date_max' => $date_max,
                'status_id' => $status_id,
                'skip' => $skip,
                'limit' => $limit,
                'order' => $sortBy,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getFinancingUnitSubmission(
        $user_id,
        $limit,
        $skip,
        $sort_by,
        $keyword = null
    ) {
        $response = Request::route('contracts.financing-unit-submission', $this->client)
            ->queryParams([
                'cust_id' => $user_id,
                'skip' => $skip,
                'limit' => $limit,
                'order' => $sort_by,
                'no_kontrak' => $keyword,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getFinancingUnitSubmissionItem(
        $user_id,
        $contract_no,
        $limit,
        $skip,
        $sort_by
    ) {
        $response = Request::route('contracts.financing-unit-submission.item', $this->client)
            ->queryParams([
                'cust_id' => $user_id,
                'no_kontrak' => $contract_no,
                'skip' => $skip,
                'limit' => $limit,
                'order' => $sort_by,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getCities()
    {
        return Request::route('location.all-cities', $this->client)->send()->json(false);
    }

    /**
     * @param int $skip
     * @param int $limit
     * @param ?string $order
     * @param ?string $keyword
     * @return object|null
     * @since CR2025
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example {
     * "COUNTRY_ID": "001",
     * "CITY_ID": "0013406",
     * "STATE_ID": "00134",
     * "DESCRIPTION": "PESISIR SELATAN",
     * "F_ACTIVE": "Y",
     * "USER_UPD": "HKY",
     * "DATE_UPD": "01-OCT-19",
     * "TIME_UPD": "131708",
     * "CITY_OJK": "3406",
     * "MAP_CITY": null,
     * "CITY_SLIK": "3406",
     * "CITY_TABLEAU": "PAINAN",
     * "F_AAB": "Y",
     * "ROWINDEX": "1"
     * },
     */
    public function getCitiesV2(int $skip = self::DEFAULT_SKIP, int $limit = self::DEFAULT_LIMIT, ?string $order = null, string $keyword = '')
    {
        return Request::route('v2.location.all-cities', $this->client)
            ->queryParams([
                'skip' => $skip,
                'limit' => $limit,
                'order' => $order,
                'keyword' => $keyword,
            ])
            ->send()
            ->json(false);
    }

    /**
     * @param $customerId
     * @param $skip
     * @param $limit
     * @param $order
     * @param int|null $timestamp
     * @param string|null $keyword
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     * {
     * 'status': true,
     * 'code': 'S_GetData',
     * 'message': 'Success',
     * 'count': 10,
     * 'data': [
     * {
     * 'AGREE_NO': '30707000094',
     * 'SERIAL_NO': 'JNBCGB45A5AT00179',
     * 'BTM': 'NISSAN DUMP TRUCK CGB45ATHN',
     * 'POLIS_NO': 'HEMC05QX2B-0802',
     * 'ROWINDEX': '1'
     * }
     * ]
     * }
     */
    public function getFinancingUnitOfInsurance($customerId, $skip, $limit, $order, ?int $timestamp, ?string $keyword)
    {
        $response = Request::route('insurances.financing-units.list', $this->client)
            ->queryParams([
                'cust_id' => $customerId,
                'skip' => $skip ?? self::DEFAULT_SKIP,
                'limit' => $limit ?? self::DEFAULT_LIMIT,
                'order' => $order ?? self::DEFAULT_ORDER,
                'timestamp' => $timestamp,
                'param' => $keyword,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @param $customerId
     * @param $skip
     * @param $limit
     * @param $order
     * @param string|null $keyword
     * @return array|stdClass|null
     * @since CR2025
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     * {
     * "AGREE_NO": "31007000578",
     * "SERIAL_NO": "JNBCWB45AAAL02507",
     * "BTM": "NISSAN DUMP TRUCK CWB45ALDN",
     * "POLIS_NO": "030510000077",
     * "YEAR": "2010",
     * "CITY_ID": "0015401",
     * "CITY_NAME": "KUTAI",
     * "EMAIL_PROVIDER": "care@abb.ac.id",
     * "EMAIL_CC": "service@sanf.co.id,faris.aizy@sanf.co.id",
     * "TIMESTAMP": "15-07-2010",
     * "ROWINDEX": "1"
     * },
     */
    public function getFinancingUnitOfInsuranceV2($customerId, $skip, $limit, $order, ?string $keyword)
    {
        $response = Request::route('v2.insurances.financing-units.list', $this->client)
            ->pathParams([
                'cust_id' => $customerId,
            ])
            ->queryParams([
                'skip' => $skip ?? self::DEFAULT_SKIP,
                'limit' => $limit ?? self::DEFAULT_LIMIT,
                'order' => $order ?? self::DEFAULT_ORDER,
                'param' => $keyword,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @param $customerId
     * @param $skip
     * @param $limit
     * @param $order
     * @param int|null $timestamp
     * @param string|null $keyword
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     * {
     * 'status': true,
     * 'code': 'S_GetData',
     * 'message': 'Success',
     * 'count': 10,
     * 'data': [
     * {
     * 'AGREE_NO': '41205000882',
     * 'SERIAL_NO': '19340',
     * 'BTM': 'KOMATSU BULLDOZER D375A-5',
     * 'YEAR': '2010',
     * 'ROWINDEX': '1'
     * }
     * ]
     * }
     */
    public function getFinancingUnitOfInvoiceCollection($customerId, $skip, $limit, $order, ?int $timestamp, ?string $keyword)
    {
        $response = Request::route('invoice-collections.financing-units.list', $this->client)
            ->queryParams([
                'cust_id' => $customerId,
                'skip' => $skip ?? self::DEFAULT_SKIP,
                'limit' => $limit ?? self::DEFAULT_LIMIT,
                'order' => $order ?? self::DEFAULT_ORDER,
                'timestamp' => $timestamp,
                'param' => $keyword,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @param $customerId
     * @param $skip
     * @param $limit
     * @param $order
     * @param int|null $timestamp causes error, unused
     * @param string|null $keyword
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     * @example
     * {
     * "status": true,
     * "code": "S_GetData",
     * "message": "Success",
     * "total": 14,
     * "count": 10,
     * "data": [
     * {
     * "AGREE_NO": "31312000685",
     * "SERIAL_NO": "J60151",
     * "BTM": "KOMATSU EXCAVATOR PC200-8 J60151",
     * "YEAR": "2013",
     * "TIMESTAMP": "01-03-2016",
     * "ROWINDEX": "1"
     * },
     * ]
     * }
     */
    public function getFinancingUnitOfInvoiceCollectionV2($customerId, $skip, $limit, $order, ?int $timestamp, ?string $keyword)
    {
        $response = Request::route('v2.invoice-collections.financing-units.list', $this->client)
            ->pathParams([
                'cust_id' => $customerId,
            ])
            ->queryParams([
                'skip' => $skip ?? self::DEFAULT_SKIP,
                'limit' => $limit ?? self::DEFAULT_LIMIT,
                'order' => $order ?? self::DEFAULT_ORDER,
                'timestamp' => $timestamp, // error core, unused
                'param' => $keyword,
            ])
            ->send();

        return $response->json(false);
    }

    /**
     * @param string $email
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getAssigneeSurvey(string $email)
    {
        $response = Request::route('assignee-survey', $this->client)
            ->queryParams(['email' => $email])->send();

        return $response->json(false);
    }

    /**
     * @param string $email
     * @param int $limit
     * @param int $skip
     * @param string $order
     * @param string|null $regNo
     * @return array|stdClass|null
     * @throws EndpointNotDefinedException
     * @throws GuzzleException
     */
    public function getSurveys(
        string $email,
        int $limit,
        int $skip,
        string $order,
        int $statusId = null
    ) {
        $response = Request::route('surveys', $this->client)
            ->queryParams([
                'email' => $email,
                'limit' => $limit,
                'skip' => $skip,
                'order' => $order,
                'status_id' => $statusId,
            ])->send();

        return $response->json(false);
    }

    public function findSurveyByEmailAndContractNo(string $email, string $contractNo)
    {
        $response = Request::route('surveys', $this->client)
            ->queryParams([
                'email' => $email,
                'limit' => self::DEFAULT_LIMIT,
                'skip' => 0,
                'order' => self::DEFAULT_ORDER,
                'reg_no' => $contractNo,
            ])->send();

        return $response->json(false);
    }

    public function addSurvey($input)
    {
        $response = Request::route('surveys.add', $this->client)
            ->json($input)
            ->send();

        return $response->json(false);
    }

    public function getAvailableESignUser(string $email)
    {
        $response = Request::route('e-sign.user', $this->client)
            ->queryParams(['email' => $email])
            ->send();

        return $response->json();
    }

    public function updateESignUserStatus(string $email)
    {
        $response = Request::route('e-sign.user.update-status', $this->client)
            ->json(['email' => $email])
            ->send();

        return $response->json();
    }

    public function browseESignDocumentV2(string $email, string $keyword = null)
    {
        $response = Request::route('v2.e-sign.document.browse', $this->client)
            ->pathParams(['email' => $email])
            ->queryParams([
                'filter' => $keyword ?? '',
            ])->send();

        return $response->json();
    }

    public function browseESignDocument(string $email, string $keyword = null)
    {
        $response = Request::route('e-sign.document.browse', $this->client)
            ->queryParams([
                'email' => $email,
                'filter' => $keyword ?? '',
            ])->send();

        return $response->json();
    }

    public function updateESignDocumentStatus(string $documentId)
    {
        $response = Request::route('e-sign.document.update-status', $this->client)
            ->json(['doc_id' => $documentId])
            ->send();

        return $response->json();
    }

    public function updateESignDocumentFile(string $documentId, string $documentName, string $path)
    {
        $response = Request::route('e-sign.document.update-file', $this->client)
            ->json([
                'f_download' => 'Y',
                'doc_id' => $documentId,
                'doc_name' => $documentName,
                'path_name' => $path,
            ])->send();

        return $response->json();
    }

    public function browseFinancingApplication(string $email, string $profileId)
    {
        $response = Request::route('financing-applications.browse', $this->client)
            ->queryParams([
                'userid' => $email,
                'profileid' => $profileId,
            ])->send();

        return $response->json();
    }

    public function browseRequestedDocuments(object $arguments)
    {
        $response = Request::route('request-document.browse', $this->client)
            ->queryParams([
                'cust_id' => $arguments->profile_xid,
                'doc_type' => $arguments->document_type ?? null,
                'skip' => $arguments->skip ?? self::DEFAULT_SKIP,
                'limit' => $arguments->limit ?? self::DEFAULT_LIMIT,
                'order' => $arguments->order ?? self::DEFAULT_ORDER,
                'keyword' => $arguments->keyword ?? null,
            ])
            ->send();

        return $response->json(false);
    }

    public function browseRequestedUploadDocuments(object $arguments)
    {
        $response = Request::route('request-uploaded-document.browse', $this->client)
            ->queryParams([
                'cust_id' => $arguments->profile_xid,
                'skip' => $arguments->skip ?? self::DEFAULT_SKIP,
                'limit' => $arguments->limit ?? self::DEFAULT_LIMIT,
                'order' => $arguments->order ?? self::DEFAULT_ORDER,
            ])
            ->send();

        return $response->json(false);
    }

    public function submitRequestedUploadDocument(object $arguments)
    {
        $response = Request::route('request-document.submit', $this->client)
            ->json([
                'req_no' => $arguments->request_no,
                'doc_id' => $arguments->document_id,
                'path_name' => $arguments->path,
                'filename' => $arguments->origin,
                'date_upd' => $arguments->uploaded_at,
            ])->send();

        return $response->json();
    }

    public function getUserBankAccount(string $profileXid, object $arguments = null)
    {
        $response = Request::route('bank.account', $this->client)
            ->queryParams([
                'cust_id' => $profileXid,
            ])->send();

        return $response->json(true);
    }

    public function getOCRPermission(string $email)
    {
        $response = Request::route('ocr.permission', $this->client)
            ->queryParams([
                'email' => $email,
            ])->send();

        return $response->json(true);
    }

    public function submitPlafondDisbursement(PlafondDisbursementCoreFormRequest $dto)
    {
        $response = Request::route('plafond.disbursement.create', $this->client)
            ->json([
                'cust_id' => $dto->cust_id,
                'p_code' => $dto->p_code,
                'disbursement_no' => $dto->disbursement_no,
                'plafond_id' => $dto->plafond_id,
                'bouwheer' => $dto->bouwheer,
                'bouwheer_code' => $dto->bouwheer_code,
                'amount' => $dto->amount,
                'invoices' => array_map(function ($invoices) {
                    return (array) $invoices;
                }, $dto->invoices),
                'allocations' => array_map(function ($allocation) {
                    return (array) $allocation;
                }, $dto->allocations),
                'percepatan_doc' => array_map(function ($document) {
                    return (array) $document;
                }, $dto->percepatan_doc),
                'invoice_doc' => array_map(function ($document) {
                    return (array) $document;
                }, $dto->invoice_doc),
                'pendukung_doc' => array_map(function ($document) {
                    return (array) $document;
                }, $dto->pendukung_doc),
                'created_at' => $dto->created_at,
            ])->send();

        return $response->json();
    }

    public function getPlafondDisbursement(string $clientId, string $plafondId = null)
    {
        $response = Request::route('plafond.disbursement.browse', $this->client)
            ->queryParams([
                'cust_id' => $clientId,
                'no_plafond' => $plafondId,
            ])->send();

        return $response->json(true);
    }

    public function getBowheer(string $clientId, string $bowheerName = null, string $bowheerEmail = null, int $limit = 100, int $page = 1)
    {
        $response = Request::route('bowheer.browse', $this->client)
            ->queryParams([
                'cust_id' => $clientId,
                'bowheer_name' => $bowheerName,
                'bowheer_email' => $bowheerEmail,
                'limit' => $limit,
                'page' => $page,
            ])->send();

        return $response->json(true);
    }
}
