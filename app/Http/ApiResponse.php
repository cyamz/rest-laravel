<?php

namespace App\Http;

use App\Models\ErrorCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * 通用返回值结构类
 *
 * @property int $http_code HTTP 状态码 2**|4**|5**
 * @property string $result YB返回结果 success|fail
 * @property array $ecodes 错误码
 * @property string $msg 返回信息
 * @property array $data 返回数据
 * @property string $datetime 返回时间
 * @property array $error_details 错误详情
 *
 * @property array $error_codes 错误码对象
 */
class ApiResponse
{
    private $http_code;
    private $result;
    private $ecodes;
    private $msg;
    private $data;
    private $datetime;
    private $error_details;

    // 错误码对象
    private static $error_codes = [];

    public function __construct($http_code, $result, Model | LengthAwarePaginator | Collection | array | null $data = [], $msg = '', int | array $ecodes = 0, $error_details = [])
    {
        if ($data instanceof LengthAwarePaginator) {
            $this->data = $this->handlePaginate($data);
        } elseif ($data instanceof Collection) {
            $this->data = $this->handleCollection($data);
        } elseif ($data instanceof Model) {
            $this->data = $this->handleModel($data);
        } elseif (is_array($data)) {
            // 自定义返回值结构
            // 用户角色相关的自定义 推荐使用模型中 handleModel 方法实现
            $this->data = $data;
        } elseif ($data === null) {
            $this->data = [];
        }

        $this->http_code = $http_code;
        $this->result = $result;
        $this->msg = $msg;
        $this->ecodes = is_array($ecodes) ? $ecodes : [$ecodes];
        $this->error_details = $error_details;

        $this->datetime = date('Y-m-d H:i:s');
    }

    /**
     * 处理单个模型
     *
     * @param Model $model
     * @return array
     */
    private function handleModel(Model $model): array
    {
        $model_data = $model->dataHandle(auth()->user() ?? null);

        return $model_data;
    }
    /**
     * 处理分页列表
     *
     * @param LengthAwarePaginator $paginate
     * @return array
     */
    private function handlePaginate(LengthAwarePaginator $paginate): array
    {
        $list = [];
        foreach ($paginate as $model) {
            $list[] = $this->handleModel($model);
        }

        $paginate_data = $paginate->toArray();

        $self = null;
        foreach ($paginate_data['links'] as $link) {
            if ($link['active']) {
                $self = $link['url'];
                break;
            }
        }

        return [
            'list' => $list,
            '_page' => [
                'path' => $paginate_data['path'],
                'page' => $paginate_data['current_page'],
                'pageSize' => $paginate_data['per_page'],
                'total' => $paginate_data['total'],
                'totalPage' => $paginate_data['last_page'],
            ],
            '_links' => [
                'self' => ['href' => $self],
                'first' => ['href' => $paginate_data['first_page_url']],
                'last' => ['href' => $paginate_data['last_page_url']],
                'prev' => ['href' => $paginate_data['prev_page_url']],
                'next' => ['href' => $paginate_data['next_page_url']],
            ],
        ];
    }
    /**
     * 处理无分页列表
     *
     * @param Collection $collection
     * @return array
     */
    private function handleCollection(Collection $collection): array
    {
        $list = [];
        foreach ($collection as $model) {
            $list[] = $this->handleModel($model);
        }

        return ['list' => $list];
    }

    /**
     * 获取ErrorCode对象集合
     *
     * @param int|array $ecodes
     * @return Collection
     */
    private static function getErrorCodes($ecodes)
    {
        if (!is_array($ecodes)) {
            $ecodes = [$ecodes];
        }

        $error_codes = [];
        $not_found_ecodes = [];
        foreach ($ecodes as $ecode) {
            if (isset(self::$error_codes[$ecode])) {
                $error_codes[] = self::$error_codes[$ecode];
            } else {
                $not_found_ecodes[] = $ecode;
            }
        }

        $not_found_error_codes = ErrorCode::query()->whereIn('code', $not_found_ecodes)->get();
        foreach ($not_found_error_codes as $not_found_error_code) {
            self::$error_codes[$not_found_error_code->code] = $not_found_error_code;
            $error_codes[] = $not_found_error_code;
        }

        return collect($error_codes);
    }

    /**
     * 返回成功api
     *
     * @param Model|LengthAwarePaginator|Collection|array $data 模型，分页，Collection或数组
     * @param string $msg 备注
     * @param int $http_code 200|201
     */
    public static function success(Model | LengthAwarePaginator | Collection | array | null $data, string $msg = '', int $http_code = 200)
    {
        if (!in_array($http_code, [200, 201])) {
            throw new \Exception('错误的HTTP状态码,需要200|201', 500);
        }

        (new static($http_code, 'success', $data, $msg, 0))->apiReturn();
    }
    /**
     * 返回参数失败api
     *
     * @param int $http_code 4**
     * @param string $msg 备注
     * @param int | array $ecodes 详情错误码，默认HTTP错误码，可额外设置
     * @param array $error_details 错误详情
     */
    public static function missing(int $http_code = 400, string $msg = '', array $error_details = [], int | array $ecodes = [])
    {
        if ($http_code < 400 || $http_code >= 500) {
            throw new \Exception('错误的HTTP状态码,需要4**', 500);
        }

        if (!$ecodes) {
            $ecodes = [$http_code];
        }

        $error_codes = self::getErrorCodes($ecodes);
        (new static($http_code, 'fail', $error_codes, $msg, $ecodes, $error_details))->apiReturn();
    }
    /**
     * 返回系统异常api
     *
     * @param int $http_code 5**
     * @param string $msg 备注
     * @param int | array $ecodes 详情错误码，默认HTTP错误码，可额外设置
     * @param array $error_details 错误详情
     */
    public static function error(int $http_code = 500, string $msg = '', array $error_details = [], int | array $ecodes = [])
    {
        if ($http_code < 500 || $http_code >= 600) {
            throw new \Exception('错误的HTTP状态码,需要5**', 500);
        }

        if (!$ecodes) {
            $ecodes = [$http_code];
        }

        $error_codes = self::getErrorCodes($ecodes);
        (new static($http_code, 'fail', $error_codes, $msg, $ecodes, $error_details))->apiReturn();
    }
    public static function exceptionReturn(int $http_code, string $msg = '', array $error_details = [], int | array $ecodes = [])
    {
        if ($http_code >= 400 && $http_code < 500) {
            self::missing($http_code, $msg, $error_details, $ecodes);
        } elseif ($http_code >= 500 && $http_code < 600) {
            self::error($http_code, $msg, $error_details, $ecodes);
        } else {
            self::error(500, '错误的HTTP状态码: ' . $http_code, $error_details, $ecodes);
        }
    }

    /**
     * 返回api json
     */
    public function apiReturn()
    {
        // 使用try catch,防止"异常判断Handle"进入死循环
        try {
            $request_id = request()->request_id ?? '';

            if ($this->result == 'success') {
                $data = $this->data;
            } else {
                $data = [
                    'errors' => $this->data,
                    'details' => $this->error_details,
                ];
            }

            $result = [
                'data' => (object) $data,
                'msg' => $this->msg,
                'datetime' => $this->datetime,
                'request_id' => $request_id,
            ];

            http_response_code($this->http_code);
        } catch (\Throwable $th) {
            $this->http_code = 500;
            $result = [
                'result' => 'fail',
                'ecode' => 500,
                'data' => ['errors' => [], 'details' => []],
                'msg' => '返回类异常',
                'datetime' => date('Y-m-d H:i:s'),
                'request_id' => '',
            ];
        }

        if (PERFORMANCE_MONITORING) {
            $result['_time'] = microtime(true) - request()->request_time;
            if ($result['_time'] > 2) {
                Log::channel('overtime')->error('');
            } elseif ($result['_time'] > 1.5) {
                Log::channel('overtime')->info('');
            }
        }
        $result['_system_time'] = time();

        $json_unescaped_unicode = json_encode($result, JSON_UNESCAPED_UNICODE);
        if ($this->http_code < 400) {
            Log::channel('info_response')->info($json_unescaped_unicode . ";\n");
        } elseif ($this->http_code < 500) {
            Log::channel('info_response')->notice($json_unescaped_unicode . ";\n");
        } else {
            Log::channel('error_response')->error($json_unescaped_unicode . ";\n");
        }

        $json_result = json_encode($result);

        if (request()->encryption ?? false) {
            echo request()->encryption->clientEncrypt($json_result);
        } else {
            echo $json_result;
        }

        exit;
    }

}
