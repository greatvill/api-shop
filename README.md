alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
sail up -d
sail artisan make:model Category -mfsc
