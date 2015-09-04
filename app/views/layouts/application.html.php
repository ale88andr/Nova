<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?=$app->title('title')?></title>
    <?= \Nova\Helpers\Assets::import('app.css') ?>
      <?= \Nova\Helpers\Assets::import('app.js') ?>
    <script type="application/javascript" src="../app/assets/js/app.js"></script>
<!--    --><?//=Environment\Helpers\Html::useCdn('http://ajax.aspnetcdn.com/ajax/jQuery/jquery-2.1.4.min.js');?>
<!--    --><?//=Environment\Helpers\Html::useCdn('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css');?>
<!--    --><?//=Environment\Helpers\Html::useCdn('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js');?>
  </head>
  <body style="padding-top: 80px;">
    <!-- header -->
<!--    --><?// include_once $app->viewsPath() . 'common/header.html.php' ?>
    <!-- page content -->
    <div class="container">
      <section id='content'>
        <?= $app->content ?>
      </section>
    </div>
  </body>
</html>