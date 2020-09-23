Vagrant.require_version ">= 2.2.4"
Vagrant.configure(2) do |config|

    config.vm.box = "ubuntu/focal64"

    config.vm.network "forwarded_port", guest: 80, host: 9001, id: "http"
    config.vm.network "forwarded_port", guest: 22, host: 9023, id: "ssh"

    config.vm.provider "virtualbox" do |v|
        v.gui = false
        v.cpus = 4
        v.memory = 2048
        v.name = "NW"
        v.default_nic_type = "virtio"

        v.customize ["modifyvm", :id, "--uartmode1", "client", "NUL"]
    end

    config.vm.synced_folder ".", name: "vagrant"
    config.vm.provision "shell", path: "bin/provision"
    config.vm.provision "shell", inline: "service nginx start", run: 'always'
end
