<footer>
    <div class="footer clearfix mb-0 text-muted">
        <div class="float-start">
            <p>{{ now()->year }} &copy; nado.ma</p>
        </div>
    </div>
</footer>

<div class="modal fade text-left" id="md-del-row" tabindex="-1" role="dialog" aria-labelledby="modalDel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title white" id="modalDel">Confirm deletion </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
              Are you sure you want to delete the item ?
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning"  data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Close</span>
                </button>

                <form action="" method="POST">
                  @csrf
                  @method("DELETE")
                  <button  class="btn btn-light-secondary ml-1" >
                      <i class="bx bx-check "></i>
                      <span>Delete</span>
                  </button>
                </form>
            </div>
        </div>
    </div>
  </div>

<script src="{{ asset('assets/back/assets/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/back/assets/js/app.js') }}"></script>
@yield('script')
<script>



// Array.from(elements).forEach(function(ele) { ele.addEventListener('click', myFunction); });
document.querySelectorAll('.btn-del-row').forEach(el => el.addEventListener('click', event => {
    document.querySelector('#md-del-row form').setAttribute("action", event.target.getAttribute("data-url"));
}));


/*  old browsers
var elements = document.getElementsByClassName("btn-del-row");
for (var i = 0; i < elements.length; i++) {
    elements[i].addEventListener('click', function() {
        alert(this.getAttribute("data-url"));
    }, false);
}
  */
  new DataTable('#example', {
    order: [[0, (window.__dtOrderDir || 'desc')]],
    initComplete: function () {
        this.api()
            .columns()
            .every(function () {
                let column = this;
                let title = column.footer().textContent;

                // Create input element
                let input = document.createElement('input');
                input.placeholder = title;
                column.footer().replaceChildren(input);

                // Event listener for user input
                input.addEventListener('keyup', () => {
                    if (column.search() !== this.value) {
                        column.search(input.value).draw();
                    }
                });
            });
    }
});




</script>
