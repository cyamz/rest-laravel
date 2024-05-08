<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Api通用控制器
 */
class MyApiController extends Controller
{
    protected $page_size = DEFAULT_PAGE_SIZE;

    public function getDefaultPageSize(): int
    {
        return $this->page_size;
    }
    public function setDefaultPageSize(int $page_size): self
    {
        if ($page_size < 1) {
            throw new \Exception('获取列表条数至少为1', 500);
        }

        $this->page_size = $page_size;
        return $this;
    }

    public function onlyRoles($roles, $error_message = '角色权限不匹配')
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        if (request()->user()->isNotRoles($roles) ?? false) {
            $this->missingDeny($error_message);
        }
    }

    /**
     * 成功
     *
     * @param Model|LengthAwarePaginator|Collection|array $data 模型，分页，Collection或数组
     * @param string $resume 备注
     * @param int $http_code 200|201
     */
    protected function success(Model | LengthAwarePaginator | Collection | array | null $data = [], string $resume = '成功', int $http_code = 200)
    {
        ApiResponse::success($data, $resume, $http_code);
    }
    /**
     * 参数错误
     *
     * @param integer $http_code 4**
     * @param string $resume 备注
     * @param array $error_details
     * @param array $ecodes
     */
    protected function missing($http_code = 404, $resume = '丢失信息', $error_details = [], $ecodes = [])
    {
        ApiResponse::missing($http_code, $resume, $error_details, $ecodes);
    }
    /**
     * 系统错误
     *
     * @param integer $http_code 5**
     * @param string $resume 备注
     * @param array $error_details 错误详情
     * @param array $ecodes
     */
    protected function error($http_code = 500, $resume = '错误', $error_details = [], $ecodes = [])
    {
        ApiResponse::error($http_code, $resume, $error_details, $ecodes);
    }

    /**
     * 创建成功
     *
     * @param Model|LengthAwarePaginator|Collection|array $data 模型，分页，Collection或数组
     * @param string $resume 备注
     */
    protected function successCreate(Model | LengthAwarePaginator | Collection | array | null $data, $resume = '创建成功')
    {
        $this->success($data, $resume, 201);
    }

    /**
     * 400
     *
     * @param string $resume
     * @param array $error_details
     * @param array $ecodes
     */
    protected function missingSyntax($resume = '请求语法错误', $error_details = [], $ecodes = [])
    {
        $this->missing(400, $resume, $error_details, $ecodes);
    }
    /**
     * 401 权限错误
     *
     * @param string $resume
     * @param array $error_details
     * @param array $ecodes
     */
    protected function missingAuth($resume = '权限错误', $error_details = [], $ecodes = [])
    {
        $this->missing(401, $resume, $error_details, $ecodes);
    }
    /**
     * 403 拒绝访问
     *
     * @param string $resume
     * @param array $error_details
     * @param array $ecodes
     */
    protected function missingDeny($resume = '拒绝访问', $error_details = [], $ecodes = [])
    {
        $this->missing(403, $resume, $error_details, $ecodes);
    }
    /**
     * 404 资源未找到
     *
     * @param string $resume
     * @param array $error_details
     * @param array $ecodes
     */
    protected function missingMiss($resume = '资源未找到', $error_details = [], $ecodes = [])
    {
        $this->missing(404, $resume, $error_details, $ecodes);
    }
    /**
     * 409 资源正在被使用
     *
     * @param string $resume
     * @param array $error_details
     * @param array $ecodes
     */
    protected function missingConflict($resume = '资源正在被使用', $error_details = [], $ecodes = [])
    {
        $this->missing(409, $resume, $error_details, $ecodes);
    }
    /**
     * 410 资源已不存在
     *
     * @param string $resume
     * @param array $error_details
     * @param array $ecodes
     */
    protected function missingNoLongerExist($resume = '资源已不存在', $error_details = [], $ecodes = [])
    {
        $this->missing(410, $resume, $error_details, $ecodes);
    }
    /**
     * 412 提供的数据结构错误
     *
     * @param string $resume
     * @param array $error_details
     * @param array $ecodes
     */
    protected function missingDataStructure($resume = '提供的数据结构错误', $error_details = [], $ecodes = [])
    {
        $this->missing(412, $resume, $error_details, $ecodes);
    }

    /**
     * 500 服务器错误
     *
     * @param string $resume
     * @param array $error_details
     * @param array $ecodes
     */
    protected function errorError($resume = '服务器错误', $error_details = [], $ecodes = [])
    {
        $this->error(500, $resume, $error_details, $ecodes);
    }

    /**
     * 501 请求方法未实现
     *
     * @param string $resume
     * @param array $error_details
     * @param array $ecodes
     */
    protected function errorNotAchieve($resume = '请求方法未实现', $error_details = [], $ecodes = [])
    {
        $this->error(501, $resume, $error_details, $ecodes);
    }
    /**
     * 503 服务目前不可用
     *
     * @param string $resume
     * @param array $error_details
     * @param array $ecodes
     */
    protected function errorNotAvaliable($resume = '服务目前不可用', $error_details = [], $ecodes = [])
    {
        $this->error(503, $resume, $error_details, $ecodes);
    }

}
