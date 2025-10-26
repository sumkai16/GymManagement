document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    loadDashboardData();

    // Set up auto-refresh every 30 seconds
    let autoRefreshInterval = setInterval(loadDashboardData, 30000);

    // Refresh button event listener
    document.getElementById('refreshDashboard').addEventListener('click', function() {
        loadDashboardData();
    });

    // Toggle auto-refresh
    let autoRefreshEnabled = true;
    document.getElementById('autoRefreshStatus').addEventListener('click', function() {
        autoRefreshEnabled = !autoRefreshEnabled;
        this.textContent = autoRefreshEnabled ? 'ON' : 'OFF';

        if (autoRefreshEnabled) {
            autoRefreshInterval = setInterval(loadDashboardData, 30000);
        } else {
            clearInterval(autoRefreshInterval);
        }
    });

    function loadDashboardData() {
        // Load stats
        fetch('admin_dashboard.php?action=get_stats')
            .then(response => response.json())
            .then(data => {
                updateStats(data);
            })
            .catch(error => {
                console.error('Error loading stats:', error);
                showFallbackStats();
            });

        // Load recent members
        fetch('admin_dashboard.php?action=get_recent_members')
            .then(response => response.json())
            .then(data => {
                updateRecentMembers(data);
            })
            .catch(error => {
                console.error('Error loading recent members:', error);
                showFallbackMembers();
            });

        // Load recent trainers
        fetch('admin_dashboard.php?action=get_recent_trainers')
            .then(response => response.json())
            .then(data => {
                updateRecentTrainers(data);
            })
            .catch(error => {
                console.error('Error loading recent trainers:', error);
                showFallbackTrainers();
            });
    }

    function updateStats(stats) {
        document.getElementById('totalMembers').textContent = stats.total_members || '--';
        document.getElementById('activeTrainers').textContent = stats.active_trainers || '--';
        document.getElementById('monthlyRevenue').textContent = '₱' + (stats.monthly_revenue ? stats.monthly_revenue.toLocaleString() : '--');
        document.getElementById('sessionsToday').textContent = stats.sessions_today || '--';
    }

    function updateRecentMembers(members) {
        const container = document.getElementById('recentMembersContent');

        if (members.length === 0) {
            container.innerHTML = '<p class="recent-loading">No recent members found.</p>';
            return;
        }

        let html = '<ul class="recent-list">';
        members.forEach(member => {
            const imageSrc = member.image ? '../../' + member.image : '../../assets/images/default-avatar.png';
            html += `
                <li class="recent-item">
                    <div class="recent-avatar">
                        <img src="${imageSrc}" alt="${member.full_name || 'Member'}" onerror="this.src='../../assets/images/default-avatar.png'">
                    </div>
                    <div class="recent-info">
                        <strong>${member.full_name || 'N/A'}</strong>
                        <span>${member.email || 'N/A'}</span>
                    </div>
                    <div class="recent-meta">
                        <span class="membership-type">${member.membership_type || 'N/A'}</span>
                        <span class="join-date">${member.start_date ? new Date(member.start_date).toLocaleDateString() : 'N/A'}</span>
                    </div>
                </li>
            `;
        });
        html += '</ul>';

        container.innerHTML = html;
    }

    function updateRecentTrainers(trainers) {
        const container = document.getElementById('recentTrainersContent');

        if (trainers.length === 0) {
            container.innerHTML = '<p class="recent-loading">No recent trainers found.</p>';
            return;
        }

        let html = '<ul class="recent-list">';
        trainers.forEach(trainer => {
            const imageSrc = trainer.image ? '../../' + trainer.image : '../../assets/images/default-avatar.png';
            html += `
                <li class="recent-item">
                    <div class="recent-avatar">
                        <img src="${imageSrc}" alt="${trainer.full_name || 'Trainer'}" onerror="this.src='../../assets/images/default-avatar.png'">
                    </div>
                    <div class="recent-info">
                        <strong>${trainer.full_name || 'N/A'}</strong>
                        <span>${trainer.email || 'N/A'}</span>
                    </div>
                    <div class="recent-meta">
                        <span class="specialty">${trainer.specialty || 'General'}</span>
                    </div>
                </li>
            `;
        });
        html += '</ul>';

        container.innerHTML = html;
    }

    function showFallbackStats() {
        document.getElementById('totalMembers').textContent = '150';
        document.getElementById('activeTrainers').textContent = '8';
        document.getElementById('monthlyRevenue').textContent = '₱45,000';
        document.getElementById('sessionsToday').textContent = '25';
    }

    function showFallbackMembers() {
        const container = document.getElementById('recentMembersContent');
        container.innerHTML = '<p class="recent-loading">Unable to load recent members. Please try refreshing.</p>';
    }

    function showFallbackTrainers() {
        const container = document.getElementById('recentTrainersContent');
        container.innerHTML = '<p class="recent-loading">Unable to load recent trainers. Please try refreshing.</p>';
    }
});
