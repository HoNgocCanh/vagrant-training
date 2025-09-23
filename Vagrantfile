Vagrant.configure("2") do |config|
    config.vm.box = "ubuntu/focal64"
    config.vm.box_version = "20240821.0.1"
    config.vm.network "private_network", ip: "192.168.33.10"
    config.vm.network "public_network"
  
    config.vm.synced_folder "./sources", "/vagrant"
  
    config.vm.provider "virtualbox" do |vb|
      vb.gui = true
      vb.memory = "4096"
      vb.cpus = 2
    end
  
    config.vm.provision "shell", inline: <<-SHELL
      apt-get update
      apt-get install -y apache2
      sudo apt-get install -y docker.io docker-compose
      sudo usermod -aG docker vagrant
      sudo apt install -y make git net-tools
    SHELL
  
    config.vm.boot_timeout = 600
  end
  