<div class="page-limit-width">
    <form action="" method="POST">
        @csrf

        {!! $form->render() !!}

        <div class="form-group">
            <button class="btn" type="submit">submit</button>
        </div>

    </form>
</div>
