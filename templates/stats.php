<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays stats about your fill-ups.
 *
 * The following variables are available to you:
 *   - $doc: the PHP class wrapping the spreadsheet reference
 *   - $message: custom message to display
 */

include('header.php');
?>
<header id="top">
  <hgroup>
    <h1><a href="<?php echo BASE_URL . '?' . GlApp::GET_ID . '=' . $_GET[GlApp::GET_ID]; ?>"><?php echo $doc->title(); ?></a></h1>
    <h2>Stats</h2>
  </hgroup>
</header>
<article>
  <?php
  if ($message == null) {
    $message = "At the gas station now? Add a <a href=" . $doc->newUrl() . ">new entry</a> to the log.";
  }
  
  // Get the 'stats' table from the current document
  $stats = $doc->stats();
  ?>
  
  <!-- Strange rendering issue where there will be a large gap between article
       and header backgrounds...This empty paragraph seems to fix it.
  -->
  <p></p>
  
  <fieldset class="stats">
    <div class="message">
      <p><?php echo $message; ?></p>
    </div>
    <?php
      $lastDatetime = strtotime($stats['last']['datetime']);
      $dateStr = date(GlApp::DATE_FORMAT_FULL, $lastDatetime);
    ?>
    <legend>Most Recent Fill-up, <span class="datetime" title="<?php echo date(GlApp::DATE_FORMAT, $lastDatetime); ?>"><?php echo $dateStr; ?></span></legend>
    
    <div class="statdesc">
      <p>The following stats are based on your most recent fill-up at 
      <?php
        if ($stats['last']['location'] == $stats['all']['location'])
          echo 'your <span class="location" title="' . $stats['all']['location'] . '">favorite station</span> ';
        else
          echo $stats['last']['location'] . ' ';
        
        $dateStr = getFriendlyDatetime($lastDatetime, $stats);
        echo '<span class="datetime" title="' . date(GlApp::DATE_FORMAT, $lastDatetime) . '">' . $dateStr . '</span>';
      ?>.
      </p>
    </div>
    
    <!-- Gas mileage -->
    <div class="statrow mpg">
      <?php
      $change = $stats['last']['mpg'] - $stats['all']['mpg'];
      
      // These are only used to change the text description. 0.25mpg and below
      // is a "slight" change, more than 2 is a "significant" change.
      $thresholdText = getThresholdText($change, 0.25, 2, 'increased', 'dropped');
      
      // Determine the brightness of the color. A change of > 0.75mpg will be
      // visually apparent.
      $color = getTrendColor($change, 0.75);
      
      ?>
      <div class="content">
        <div class="value mpg" style="color: <?php echo $color; ?>" title="<?php echo $stats['last']['mpg']; ?>">
          <?php echo getMpg($stats['last']['mpg']); ?>
        </div>
        <div class="desc">
          <p>
          During your last tank of gas, your mileage <span class="<?php echo ($change >= 0) ? 'better' : 'worse'; ?>"><?php echo $thresholdText; ?></span>, by <span class="mpg" title="<?php echo abs($change); ?>"><?php echo getMpg(abs($change)); ?></span>. This is
          <?php
            $change = (($stats['last']['mpg'] * 100) / $stats['all']['mpg']) - 100;
            
            if ($change >= 0)
              echo '<span class="better">';
            else
              echo '<span class="worse">';
            
            echo '<span class="percent" title="' . abs($change) . '">' . abs(getPercent($change)) . '</span>';
            
            if ($change >= 0)
              echo ' better';
            else
              echo ' worse';
            echo '</span>';
            ?>
            than your all-time average <span class="mpg" title="<?php echo $stats['all']['mpg']; ?>"><?php echo getMpg($stats['all']['mpg']); ?></span>. 
          </p>
        </div>
      </div>
      <div class="arrow">
        <?php echo getArrowHtml($color, $change); ?>
      </div>
    </div>
    
    <!-- Trip distance -->
    <div class="statrow tripdistance">
      <?php
      $change = $stats['last']['tripdistance'] - $stats['all']['tripdistance'];
      
      // These are only used to change the text description. 10 miles and below
      // is a "slight" change, more than 50 miles is a "significant" change.
      $thresholdText = getThresholdText($change, 10, 50, 'farther', 'less distance');
      
      // Determine the brightness of the color. A change of > 50mi will be
      // visually apparent.
      $color = getTrendColor($change, 50);
      
      ?>
      <div class="content">
        <div class="value distance" style="color: <?php echo $color; ?>" title="<?php echo $stats['last']['tripdistance']; ?>">
          <?php echo getMiles($stats['last']['tripdistance']); ?>
        </div>
        <div class="desc">
          <p>
          During your last tank of gas, you went <span class="<?php echo ($change >= 0) ? 'better' : 'worse'; ?>"><?php echo $thresholdText; ?></span> between fill-ups. Your trip distance was
          <?php
            if ($change >= 0)
              echo '<span class="better">';
            else
              echo '<span class="worse">';
          ?>
          <span class="distance" title="<?php echo abs($change); ?>"><?php echo getMiles(abs($change)); ?></span>
          <?php
            if ($change >= 0)
              echo ' farther</span>';
            else
              echo ' shorter</span>';
          ?> than your average trip distance of <span class="distance" title="<?php echo $stats['all']['tripdistance']; ?>"><?php echo getMiles($stats['all']['tripdistance']); ?></span>.
          </p>
        </div>
      </div>
      <div class="arrow">
        <?php echo getArrowHtml($color, $change); ?>
      </div>
    </div>
    
    <!-- Pump price -->
    <div class="statrow cost">
      <?php
      $change = $stats['last']['cost'] - $stats['month']['cost'];
      
      // These are only used to change the text description. $3 and below is a 
      // "slight" change, more than $10 is a "significant" change.
      $thresholdText = getThresholdText($change, 3, 10, 'more', 'less', '', '');
      
      // Determine the brightness of the color. A change of > $7.50 will be
      // visually apparent.
      $color = getTrendColor(-$change, 7.5);
      
      ?>
      <div class="content">
        <div class=" value cost" style="color: <?php echo $color; ?>" title="<?php echo $stats['last']['cost']; ?>">
          <?php echo getMoney($stats['last']['cost']); ?>
        </div>
        <div class="desc">
          <p>
          During your last tank of gas, you spent <span class="<?php echo ($change >= 0) ? 'worse' : 'better'; ?>"><span class="cost" title="<?php echo abs($change); ?>"><?php echo getMoney(abs($change)); ?></span> <?php echo $thresholdText; ?></span> than your one-month average of <span class="cost" title="<?php echo $stats['month']['cost']; ?>"><?php echo getMoney($stats['month']['cost']); ?></span>.
          This is
          <?php 
            $change = $stats['last']['cost'] - $stats['previous']['cost'];
          ?>
          <span class="<?php echo ($change >= 0) ? 'worse' : 'better'; ?>"><span class="cost" title="<?php echo $change; ?>"><?php
            if ($change >= 0)
              echo getMoney(abs($change)) . '</span> more</span>';
            else
              echo getMoney(abs($change)) . '</span> less</span>';
          ?></span> than your previous fill-up.
          </p>
        </div>
      </div>
      <div class="arrow">
        <?php echo getArrowHtml($color, $change); ?>
      </div>
    </div> <!-- end statrow -->
  
    <!-- Cost per day -->
    <div class="statrow costperday">
      <?php
      $change = $stats['last']['costperday'] - $stats['month']['costperday'];
      
      // These are only used to change the text description. $1 and below is a
      // "slight" change, more than $3 is a "significant" change.
      $thresholdText = getThresholdText($change, 1, 3, 'more', 'less', '', '');
      
      // Determine the brightness of the color. A change of > $3 will be
      // visually apparent.
      $color = getTrendColor(-$change, 3);
      
      ?>
      <div class="content">
        <div class="value cost" style="color: <?php echo $color; ?>" title="<?php echo $stats['last']['costperday']; ?>">
          <?php echo getMoney($stats['last']['costperday']); ?>
        </div>
        <div class="desc">
          <p>
          During your last tank of gas, you spent <span class="<?php echo ($change >= 0) ? 'worse' : 'better'; ?>"><span class="cost" title="<?php echo abs($change); ?>"><?php echo getMoney(abs($change)); ?></span> <?php echo $thresholdText; ?></span> per day than your one-month average of <span class="cost" title="<?php echo $stats['month']['costperday']; ?>"><?php echo getMoney($stats['month']['costperday']); ?></span>.
          You spent 
          <?php 
            $change = $stats['last']['costperday'] - $stats['previous']['costperday'];
            $threshold = getThresholdText($change, 1, 3, 'more', 'less', '', '');
          ?>
          <span class="<?php echo ($change >= 0) ? 'worse' : 'better'; ?>"><span class="cost" title="<?php echo abs($change); ?>"><?php echo getMoney(abs($change)); ?></span> <?php echo $thresholdText; ?></span> per day than during your previous trip.
          </p>
          <p>For this tank, you spent <span class="cost"><?php echo getMoney($stats['last']['costpermile']); ?></span> per mile, which is
          <?php
            // Get change as a percentage
            $change = (($stats['last']['costpermile'] * 100) / $stats['month']['costpermile']) - 100;
            
            if ($change >= 0)
              echo '<span class="worse">up ';
            else
              echo '<span class="better">down ';
            
            echo '<span class="percent">' . abs(getPercent($change)) . '</span></span>';
            ?>
            from last month.
          </p>
        </div>
      </div>
      <div class="arrow">
        <?php
        // Have to set this again...
        $change = $stats['last']['costperday'] - $stats['month']['costperday'];
        echo getArrowHtml($color, $change);
        ?>
      </div>
    </div>
  </fieldset>
</article>

<?php
// We probably don't need to include jQuery, etc, actually...
//include('js.php');

include('footer.php');
?>