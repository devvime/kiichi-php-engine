<?php

namespace Devvime\Kiichi\Engine;

use Devvime\Kiichi\Engine\ViewService;
use Firebase\JWT\JWT;

class ControllerService {

    public function json($data)
    {
        echo json_encode($data);
    }

    public function bindValues($values, $model)
    {
        foreach ($values as $key => $value) {
            $model[$key] = $value;
        }
        return $model;
    }

    public function validate($request, $name, $type, $rule = '')
    {
        switch($type) {
            case('maxValue'):
                if (isset($request->$name) && strlen($request->$name) > $rule) {
                    echo json_encode(['error'=>"Input: '{$name}' exceeded the maximum character limit. Limit = {$rule}"]);
                    exit;
                }
            break;
            case('minValue'):
                if (isset($request->$name) && strlen($request->$name) < $rule) {
                    echo json_encode(['error'=>"Input: '{$name}' has not reached the minimum characters required. minimum characters = {$rule}"]);
                    exit;
                }
            break;
            case('required'):
                if (!isset($request->$name) || $request->$name == '' || $request->$name == null) {
                    echo json_encode(['error'=>"Input: '{$name}' is required!"]);
                    exit;
                }
            break;
            case('isEmail'):
                if(isset($request->$name) && !filter_var($request->$name, FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(['error'=>"this email '{$request}' is not valid."]);
                    exit;
                }
            break;
        }
    }

    public function render($file, $data = [])
    {
        header('Content-type: text/html; charset=utf-8');
        $viewService = new ViewService($data);
        $viewService->render($file, $data);
    }

    public function jwtEncrypt($value)
    {
        return JWT::encode($value, SECRET, 'HS256');
    }

    public function jwtDecrypt($value)
    {
        return JWT::decode($value, SECRET, array('HS256'));
    }

    public function version()
    {
        return round(microtime(true) * 1000);
    }

    public function paginate($req, $controller) {
        $perPage = isset($req->query->perPage) ? (int)$req->query->perPage : 5;
        $currentPage = isset($req->query->page) ? (int)$req->query->page : 1;
        $offset = ($currentPage - 1) * $perPage;
        $totalResult = $controller->count();
        $totalPages = ceil($totalResult / $perPage);
        $result = $controller->limit($perPage)->offset($offset)->get();
        return [
          "status" => 200,
          "success" => true,
          "data" => $result,
          "pagination" => [
            "totalPages" => $totalPages,
            "totalResult" => $totalResult,
            "currentPage" => $currentPage,
            "buttons" => $this->getSurroundingElements(range(1, $totalPages), $currentPage)
          ]
        ];
      }
    
      public function getSurroundingElements($array, $activeIndex) {
        $start = max(0, $activeIndex - 3);
        $end = min(count($array) - 1, $activeIndex + 3);    
        return array_slice($array, $start, $end - $start + 1);
      }

}