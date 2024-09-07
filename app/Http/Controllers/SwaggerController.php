<?php
namespace App\Http\Controllers;

use OpenApi\Annotations\Contact;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema;
use OpenApi\Annotations\Server;

/**
 *
 * @Info(
 *     version="1.0.0",
 *     title="articleManger",
 *     @Contact(
 *         email="mylxsw@aicode.cc",
 *         name="mylxsw"
 *     )
 * )
 *
 * @Server(
 *     url="http://localhost",
 *     description="開發環境",
 * )
 *
 * @Schema(
 *     schema="test",
 *     type="object",
 *     description="响应实体，响应结果统一使用该结构",
 *     title="响应实体",
 *     @Property(
 *         property="code",
 *         type="string",
 *         description="响应代码"
 *     ),
 *     @Property(property="message", type="string", description="响应结果提示")
 * )
 *
 *
 * @package App\Http\Controllers
 */
class SwaggerController
{}