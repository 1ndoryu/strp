<?php
    check_login();

    Notice::catch();

    $notices = Notice::getNotices($_SESSION['data']['ID_user']);

?>

<div id="content">
    <div class="row justify-content-between">
      <h2 class="notices-title">
        <svg
          height="24px"
          viewBox="0 -960 960 960"
          width="24px"
          fill="#000"
          version="1.1"
          id="svg1"
          sodipodi:docname="notification.svg"
          inkscape:version="1.3.2 (091e20ef0f, 2023-11-25)"
          xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
          xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
          xmlns="http://www.w3.org/2000/svg"
          xmlns:svg="http://www.w3.org/2000/svg">
          <defs
            id="defs1" />
          <sodipodi:namedview
            id="namedview1"
            pagecolor="#000"
            bordercolor="#eeeeee"
            borderopacity="1"
            inkscape:showpageshadow="0"
            inkscape:pageopacity="0"
            inkscape:pagecheckerboard="0"
            inkscape:deskcolor="#000"
            inkscape:zoom="21.541667"
            inkscape:cx="11.976789"
            inkscape:cy="12"
            inkscape:window-width="1366"
            inkscape:window-height="721"
            inkscape:window-x="0"
            inkscape:window-y="0"
            inkscape:window-maximized="1"
            inkscape:current-layer="svg1" />
          <path
            d="M160-200v-80h80v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v28q80 20 130 84.5T720-560v280h80v80H160Zm320-300Zm0 420q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80ZM320-280h320v-280q0-66-47-113t-113-47q-66 0-113 47t-47 113v280Z"
            id="path1"
            style="fill:#000;fill-opacity:1" />
        </svg>

        Mis alertas
      </h2>
        <?php if($_SESSION['data']['notify'] == 1): ?>
            <a href="mis-notificaciones/?notify=0" class="btn btn-notify">
                <i class="fa fa-bell"></i>
                Bloquear notificaciones
            </a>
          <?php else: ?>
            <a href="mis-notificaciones/?notify=1" class="btn btn-notify">
                <i class="fa fa-bell-slash"></i>
                Activar notificaciones
            </a>
        <?php endif ?>
    </div>
    <div class="notices-container">
      <?php foreach($notices as $notice): ?>
        <div class="notice-row">
          <div class="notice-icon">
            <img src="<?=Images::getImage('notification.svg', )?>" alt="">
          </div>
          <div class="notice-content">
            <div class="notice-title">
              <?php echo $notice['title']; ?>
            </div>
            <div class="notice-text">
              <?php echo $notice['text']; ?>
            </div>
            <div class="notice-date">
              Hace <?php echo timeSince($notice['date']); ?>
            </div>
          </div>
          <div class="notice-options">
              <?php if($notice['read'] == 0): ?>
                <a data-id="<?=$notice['ID_notice']?>" href="<?=$notice['link']?>" class="btn notice-view">
                    Ver
                </a>
              <?php endif ?>
              <a href="mis-notificaciones/?delete=<?=$notice['ID_notice']?>" class="btn notice-delete">
                  Eliminar
              </a>
          </div>
        </div>
      <?php endforeach ?>
    </div>
</div>