<style>
    footer {
        position: fixed;
        bottom: -0.6cm;
        left: 1.3cm;
        right: 1.3cm;
        height: 2.8cm;
        background: white;
        color: black;

        line-height: 35px;
    }

</style>
<footer>
    <h5 style="line-height : 12px;text-align: left; ">
        605 Raleigh Place SE | Washington Dc 20032
        <strong style="padding-left: 656px; color: rgb(138, 138, 138)">100% CBE | MBE | WBE | SBE |
            M-DOT</strong>
        <br>
        <u style="color: rgb(32, 34, 133)">www.precisionwall.com </u>| 202-330-0955
        <strong style="padding-left: 767px; color: rgb(138, 138, 138)"> HUB-Zone Certified</strong>
    </h5>
</footer>
<script type="text/php">
    if ( isset($pdf) ) {
          $pdf->page_script('
              $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
              $pdf->text(780, 525, "P.  $PAGE_NUM/$PAGE_COUNT", $font, 10);
          ');
      }
    </script>
