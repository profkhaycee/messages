<?php 
include 'header.php';
?>

<style>
    .product:hover{
        transform: scale(0.9,0.8);
    }
</style>

	
<section class="subscribe">

<div class="container">

    <div class="row align-items-center">

        <!-- <div class="col-lg-2 col-md-12 col-sm-12"> -->

            <!-- <h3 class="text-white">search</h3> -->

            <!-- <p class="text-white">Subscribe to keep up with fresh news and exciting updates.</p> -->

        <!-- </div> -->

        <div class="col-lg-12 col-md-12 col-sm-12">

            <form action="" method="GET" style="">

                <input type="text" name="searchInput" placeholder="search by Series name">

                <button type="submit" name="searchBtn">Search</button>

            </form>

        </div>

    </div>

</div>

</section>


		
<section class="gap church-products">
    <div class="heading">
        <!-- <img src="assets/images/heading-img.webp" alt="Heading Image"> -->
        <p></p>
        <h2>All Series</h2>
    </div>
    <div class="container-fluid">
        <div class="row mx-2">

        <?php
				if(isset($_REQUEST['searchInput'])){
					$searchInput = $action->validate($_REQUEST['searchInput']);
                    echo $action->searchSeries($searchInput);

				}else{
					 echo $action->fetchAllSeries() ;

				}
		?>
   
            <div class="d-flex justify-content-center loadmore">

                <a href="JavaScript:void(0)" class="theme-btn">Load More</a>

            </div>
        </div>
    </div>
</section>


<?php
include 'footer.php';