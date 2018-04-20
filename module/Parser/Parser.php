<?php

namespace HttpServer\Module\Parser;

class Parser {

    // 字符串解析
    public static function parseRequest($request) {
        $split = explode("\r\n\r\n", $request);
        $content = explode("\r\n", $split[0]);
        $body = $split[1];
        $firstLine = explode(' ', $content[0]);
        $headers_str_arr = array_slice($content, 1, -1);
        $headers = [];
        for ($i = 0; $i < count($headers_str_arr); $i++) {
            $item = explode(': ', $headers_str_arr[$i]);
            $headers[$item[0]] = $item[1];
        }
        return [
            'body' => $body,
            'headers' => $headers,
            'method' => $firstLine[0],
            'url' => $firstLine[1],
            'version' => $firstLine[2],
        ];
    }

    // 编译原理解析
}




