<?php
    namespace unique\yii2bs4helpers\assets;

    use yii\web\AssetBundle;

    class BootstrapIconsAssets extends AssetBundle {

        public $css = [
            'bsicons' => '//cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css',
        ];

        /**
         * Sets a particular version of the BootstrapIcons
         * @param string $version
         * @return $this
         */
        public function setVersion( $version ) {

            $this->css['bsicons'] = '//cdn.jsdelivr.net/npm/bootstrap-icons@' . $version . '/font/bootstrap-icons.css';
            return $this;
        }
    }