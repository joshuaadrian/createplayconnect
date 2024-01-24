<header id="header">

  <div class="navbar">

    <div class="grid-container">

      <div class="grid-row">

        <div class="grid-column grid-column-sm-16">
        
          <div class="branding">

            <h1 class="logo"><a href="/">CPC Intersect</a></h1>  

            <div class="branding-links">

              <ul>

                <?php

                $menu_args = array(
                  'menu'       => 'Main Menu',
                  'depth'      => 0,
                  'container'  => '',
                  'items_wrap' => '%3$s'
                );

                wp_nav_menu( $menu_args );

                ?>

              </ul>

            </div>
              
            <div class="branding-touch">
              <a href="#">
                <span class="text">Menu</span>
                <span class="bars">
                  <span class="bar-one"></span>
                  <span class="bar-two"></span>
                  <span class="bar-three"></span>
                </span>
              </a>
            </div>

          </div>
              
        </div>

      </div>

    </div>
        
  </div>

</header>