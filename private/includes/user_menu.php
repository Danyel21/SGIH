    <div class="dropdown user-profile">
            <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar"><i class="fas fa-user"></i></div>
    
            <span style="font-weight: 600; font-size: 0.875rem;"><?php echo ($_SESSION['username'] ?? ''); ?></span>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?php echo $waypath; ?>views/utilizadores/perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?php echo $geralPath; ?>logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            </ul>
        </div>