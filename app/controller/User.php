<?php
declare (strict_types = 1);

namespace app\controller;

use app\modal\UserModal;
use app\response\ErrorResponse;
use app\response\SuccessResponse;
use app\util\UUID;
use app\validate\UserValidate;
use thans\jwt\exception\JWTException;
use thans\jwt\facade\JWTAuth;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\Request;
use think\Response;

class User
{
    /**
     * 显示资源列表
     *
     * @return Response
     */
    public function index() : Response
    {
        $failure = new ErrorResponse();
        try {
            $payload = JWTAuth::auth();
        } catch (JWTException $e) {
            return json($failure->build(401, 'Unauthorized: ' . $e->getMessage()));
        }
        $uuid = $payload['uuid']->getValue();
        $modal = new UserModal();
        $user = $modal->findOrEmpty($uuid);
        if ($user->isEmpty() || $user['admin'] !== 1) return json($failure->build(403, 'Access Denied'));
        try {
            return json($modal->select());
        } catch (DataNotFoundException | ModelNotFoundException | DbException $e) {
            return json($failure->build(500, 'Query Error: ' . $e));
        }
    }

    /**
     * 保存新建的资源
     *
     * @param Request $request
     * @return Response
     */
    public function save(Request $request) : Response
    {
        $failure = new ErrorResponse();
        $success = new SuccessResponse();
        try {
            $payload = JWTAuth::auth();
        } catch (JWTException $e) {
            return json($failure->build(401, 'Unauthorized: ' . $e->getMessage()));
        }
        $uuid = $payload['uuid']->getValue();
        $modal = new UserModal();
        $user = $modal->findOrEmpty($uuid);
        if ($user->isEmpty() || $user['admin'] !== 1) return json($failure->build(403, 'Access Denied'));
        $params = $request->param();
        try {
            validate(UserValidate::class)->check($params);
        } catch (ValidateException $e) {
            return json($failure->build("400", "Bad Request: " . $e->getMessage()));
        }
        if (!$modal->where('username', $request['username'])->findOrEmpty()->isEmpty()) return json($failure->build('202', 'User already exists'));
        $uuid = UUID::create();
        $params['uuid'] = $uuid;
        $modal->save($params);
        return json($success->build("User Added Successfully"));
    }

    /**
     * 显示指定的资源
     *
     * @param  Request  $request
     * @return Response
     */
    public function read(Request $request) : Response
    {
        $failure = new ErrorResponse();
        try {
            $payload = JWTAuth::auth();
        } catch (JWTException $e) {
            return json($failure->build(401, 'Unauthorized: ' . $e->getMessage()));
        }
        $uuid = $payload['uuid']->getValue();
        $modal = new UserModal();
        $user = $modal->findOrEmpty($uuid);
        if ($request['uuid']) {
        }
    }

    /**
     * 保存更新的资源
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return Response
     */
    public function delete($id)
    {
        //
    }
}
