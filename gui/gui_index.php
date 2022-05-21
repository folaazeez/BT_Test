<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Store</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  </head>
  <body>
    <div class="container">
        
      <div class="row justify-content-md-center justify-content-lg-center bordered">
          <div class="col-md-auto col-lg-auto">      
            <h2>Sales Terminal</h2>
                  <form method="POST" onsubmit="return validate()">
                    <table class="table table-striped table-hover">
                      <thead>
                    <tr>
                      <th colspan="4"><select name="sku">
                          <option value="A">A</option>
                          <option value="B">B</option>
                          <option value="C">C</option>
                          <option value="D">D</option>
                          <option value="E">E</option>
                          </select>

                          <input id="quantity" name="quantity" type="number" />

                          <button type="submit" name="add" value="add">Add to Cart</button>
                      </th>
                      </tr>
                      <tr>
                        <th>SKU</th>
                        <th class="text-center">QUANTITY</th>
                        <th class="text-end">Price</th>
                        <th></th>
                      </tr>
                      </thead>
                      <?=$cart?>
                      <?=$footer?>
                    </table>
                  </form>     

            <form method="POST">
                <div>
                <button class="btn btn-primary" type="submit" name="clear" value="clear">Clear Cart</button>
                </div>          
            </form>     
          </div>
          <div class="col col-lg-2">
          </div>
      </div>        
    </div>
    
      <script>
        function validate() {
            if(document.getElementById('quantity').value=='') {
              alert('Please enter a quantity');
              document.getElementById('quantity').focus();
              return false;
            }
        }
      </script>
  </body>
</html>