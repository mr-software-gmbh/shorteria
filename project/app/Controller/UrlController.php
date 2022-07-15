<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Component\ResponseComponent;
use App\Component\ShorteriaComponent;
use App\Model\UrlModel;

class UrlController extends BaseController
{
    protected ShorteriaComponent $shorteriaComponent;
    private $urlModel;

    public function __construct()
    {
        parent::__construct();
        $this->shorteriaComponent = new ShorteriaComponent();
        $this->urlModel = new UrlModel();
    }

    /**
     * @throws \Exception
     */
    public function details(): bool
    {
        $isAuthenticated = false;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $isAuthenticated = !empty($_POST['token']) && $_POST['token'] === $this->config['authToken'];
            if (!$isAuthenticated) {
                $this->setFlash($this->errorMessages['wrongTokenOrNotAllFieldsSet']);
            }
        }

        $this->log->setTableAlias('l');
        $getLogs = $this->log->find(['COUNT(l.id) AS counter', 'u.short', 'u.comment', 'u.created_at', 'u.redirect_to'])
            ->join('url AS u', 'u.id', 'l.url_id', 'inner')
            ->groupBy('l.url_id')
            ->execute();

        $this->compact(['details' => $getLogs, 'isAuthenticated' => $isAuthenticated]);

        return $this->render('Url/details');
    }

    /**
     * @throws \Exception
     */
    public function store(): bool|ResponseComponent
    {
        $storedShortUrl = null;
        $isAuthenticated = false;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $isAuthenticated = !empty($_POST['token']) && $_POST['token'] === $this->config['authToken'];
            if (!$isAuthenticated || empty($_POST['url'])) {
                $this->setFlash($this->errorMessages['wrongTokenOrNotAllFieldsSet']);
            } else {
                if (!empty($_POST['shortUrl'])) {
                    $checkExist = $this->urlModel->find()->where(['short', '=', $_POST['shortUrl']])->execute('single');
                    if ($checkExist) {
                        $this->redirect($this->shorteriaComponent->getUri());
                    }
                }

                $storedShortUrl = !empty($_POST['shortUrl']) ? $_POST['shortUrl'] : $this->shorteriaComponent->getUniqueShortUrl();
                $this->urlModel->short = $storedShortUrl;
                $this->urlModel->redirect_to = $_POST['url'];
                $this->urlModel->comment = !empty($_POST['comment']) ? $_POST['comment'] : null;
                $this->urlModel->save();

                // For API request
                if (!empty($_POST['responseType']) && $_POST['responseType'] === 'json') {
                    return (new ResponseComponent())->output($this->shorteriaComponent->getUrlRoot() . '/' . $storedShortUrl, 201);
                }
            }
        }

        $this->compact([
            'shortUrl' => $this->shorteriaComponent->getUniqueShortUrl(),
            'isAuthenticated' => $isAuthenticated,
            'storedShortUrl' => $this->shorteriaComponent->getUrlRoot() . '/' . $storedShortUrl,
        ]);

        return $this->render('Url/new');
    }

    public function shortRedirect()
    {
        $shortUrl = str_replace($this->shorteriaComponent->getUrlRoot() . '/', '', $this->shorteriaComponent->getUri());
        $data = $this->urlModel->find()
            ->where(['short', '=', $shortUrl], ['deactivated_at', 'IS', 'NULL'])
            ->execute('single');

        if ($data) {
            $this->log->add($data['id']);
            $this->redirect($data['redirect_to']);
        } else {
            $this->redirect($this->config['errorRedirectPage']);
        }
    }

    public function edit()
    {
        $data = $this->urlModel->find()->where(['short', '=', $_GET['shortcode']])->execute('single');

        if (!$data) {
            $this->setFlash($this->errorMessages['editShortUrlNotFound']);
            $this->redirect('/__details');
        }

        $isAuthenticated = false;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $isAuthenticated = !empty($_POST['token']) && $_POST['token'] === $this->config['authToken'];
            if (!$isAuthenticated || empty($_POST['url'])) {
                $this->setFlash($this->errorMessages['wrongTokenOrNotAllFieldsSet']);
            } else {
                $this->urlModel->id = $data['id'];
                $this->urlModel->redirect_to = $_POST['url'];
                $this->urlModel->comment = !empty($_POST['comment']) ? $_POST['comment'] : null;
                $this->urlModel->save();

                if (!empty($_POST['responseType']) && $_POST['responseType'] === 'json') {
                    // For API request
                    $apiResponse = [
                        'success' => true,
                        'message' => 'Update has been success.',
                        'data' => [
                            'shortUrl' => $this->shorteriaComponent->getUrlRoot() . '/' . $data['short'],
                            'redirectTo' => $this->urlModel->redirect_to,
                            'comment' => $this->urlModel->comment,
                        ],
                    ];

                    return (new ResponseComponent())->json($apiResponse, 201);
                } else {
                    $this->setFlash('Update has been success.');
                    $this->redirect('/__details');
                }
            }
        }

        $this->compact(['data' => $data, 'isAuthenticated' => $isAuthenticated]);

        return $this->render('Url/edit');
    }
}
