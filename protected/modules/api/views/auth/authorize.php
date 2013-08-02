<?php $this->layout = '//layouts/main-column1'; ?>

<?php if (Yii::app()->user->isGuest) : ?>
    <?php $this->renderPartial('_partial/guest_form', array('model' => $model)); ?>
<?php else : ?>
    <?php $token = getModule('api')->token()->isAuthorize($app['client_id'], null, false); ?>
    <?php if (empty($token)) : ?>
        <?php $this->renderPartial('_partial/authorize_app', array('model' => $model, 'app' => $app, 'auth_params' => $auth_params)); ?>
    <?php else : ?>
        <?php
            $this->redirect($token->client->redirect_uri . '?access_token=' . $token->oauth_token);
            $this->layout = 'json';
            $skip = array('token_type', 'scope', 'created_at');
            $return = array();

            foreach ($token as $key => $value) {
                if (in_array($key, $skip)) continue;
                $return[$key] = $value;
            }

            echo json_encode($return);
        ?>
    <?php endif; ?>
<?php endif; ?>