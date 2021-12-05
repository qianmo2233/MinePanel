<?php
declare (strict_types = 1);

namespace app\controller;

use app\modal\UserModal;
use app\response\ErrorResponse;
use app\response\SuccessResponse;
use app\util\Encryption;
use app\util\UUID;
use app\validate\UserValidate;
use thans\jwt\facade\JWTAuth;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
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
        $uuid = JWTAuth::getPayload()['uuid']->getValue();
        $modal = new UserModal();
        $user = $modal->findOrEmpty($uuid);
        if ($user->isEmpty() || $user['admin'] !== 1) return json((new ErrorResponse)->build(403, 'Access Denied'));
        try {
            return json($modal->select());
        } catch (DataNotFoundException | ModelNotFoundException | DbException $e) {
            return json((new ErrorResponse)->build(500, 'Query Error: ' . $e));
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
        $uuid = JWTAuth::getPayload()['uuid']->getValue();
        $modal = new UserModal();
        $user = $modal->findOrEmpty($uuid);
        if ($user->isEmpty() || $user['admin'] !== 1) return json((new ErrorResponse)->build(403, 'Access Denied'));
        $params = $request->param();
        validate(UserValidate::class)->check($params);
        if (!$modal->where('username', $request['username'])->findOrEmpty()->isEmpty()) return json((new ErrorResponse)->build('202', 'User already exists'));
        $uuid = UUID::create();
        $params['uuid'] = $uuid;
        $params['password'] = Encryption::encrypt($params['password'], true);
        $modal->save($params);
        return json((new SuccessResponse)->build("User Added Successfully"));
    }

    /**
     * 显示指定的资源
     *
     * @param  Request  $request
     * @return Response
     */
    public function read(Request $request) : Response
    {
        $uuid = JWTAuth::getPayload()['uuid']->getValue();
        $modal = new UserModal();
        $user = $modal->findOrEmpty($uuid);
        if ($user->isEmpty()) return json((new ErrorResponse)->build(403, 'Access Denied'));
        if ($request['uuid']) {
            if ($user['admin'] !== 1) return json((new ErrorResponse)->build(403, 'Access Denied'));
            try {
                return json($modal->findOrFail($request['uuid']));
            } catch (DataNotFoundException | ModelNotFoundException $e) {
                return json((new ErrorResponse)->build(404, 'User Not Fount'));
            }
        } else {
            return json((new ErrorResponse)->build('400', 'Bad Request: UUID is required'));
        }
    }

    /**
     * 保存更新的资源
     *
     * @param Request $request
     * @return Response
     */
    public function update(Request $request) : Response
    {
        $uuid = JWTAuth::getPayload()['uuid']->getValue();
        $modal = new UserModal();
        $user = $modal->findOrEmpty($uuid);
        if ($user->isEmpty()) return json((new ErrorResponse)->build(403, 'Access Denied'));
        if (!$request['uuid']) return json((new ErrorResponse)->build('400', 'Bad Request: UUID is required'));
        if ($uuid === $request['uuid']) {
            if ($request['username']) $user->username = $request['username'];
            if ($request['password']) $user->password = $request['password'];
            $user->save();
        } else {
            if ($user['admin'] !== 1) return json((new ErrorResponse)->build(403, 'Access Denied'));
            $target = $modal->findOrEmpty($uuid);
            if ($target->isEmpty()) return json((new ErrorResponse)->build(404, 'User not found'));
            if ($request['username']) $target->username = $request['username'];
            if ($request['password']) $target->password = $request['password'];
            if ($request['status']) $target->status = $request['status'];
            if ($request['admin']) $target->status = $request['admin'];
            $target->save();
        }
        return json((new SuccessResponse)->build('User updated successfully'));
    }

    /**
     * 删除指定资源
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request) :Response
    {
        $uuid = JWTAuth::getPayload()['uuid']->getValue();
        $modal = new UserModal();
        $user = $modal->findOrEmpty($uuid);
        if ($user->isEmpty() || $user->admin === 0) return json((new ErrorResponse)->build(403, 'Access Denied'));
        if (!$request['uuid']) return json((new ErrorResponse)->build('400', 'Bad Request: UUID is required'));
        $target = $modal->findOrEmpty($uuid);
        if ($target->isEmpty()) return json((new ErrorResponse)->build(404, 'User not found'));
        $target->delete();
        return json((new SuccessResponse)->build('User deleted successfully'));
    }
}
