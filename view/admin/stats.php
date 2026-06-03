<h1>Statistiques détaillées</h1>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total visiteurs uniques</h3>
        <p class="big-number"><?= number_format($stats['total']) ?></p>
    </div>
    
    <div class="stat-card">
        <h3>Visiteurs aujourd'hui</h3>
        <p class="big-number"><?= $stats['today_unique'] ?></p>
    </div>
    
    <div class="stat-card">
        <h3>Pages vues aujourd'hui</h3>
        <p class="big-number"><?= $stats['today_views'] ?></p>
    </div>
    
    <div class="stat-card">
        <h3>En ligne (5min)</h3>
        <p class="big-number"><?= $stats['online'] ?></p>
    </div>
</div>

<h2>Évolution des 7 derniers jours</h2>
<table>
    <thead>
        <tr><th>Date</th><th>Visiteurs uniques</th><th>Pages vues</th></tr>
    </thead>
    <tbody>
        <?php foreach ($stats['last7days'] as $day): ?>
        <tr>
            <td><?= $day['visit_date'] ?></td>
            <td><?= $day['unique_visitors'] ?></td>
            <td><?= $day['page_views'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Top 10 des pages les plus visitées</h2>
<table>
    <thead>
        <tr><th>Page</th><th>Vues</th></tr>
    </thead>
    <tbody>
        <?php foreach ($stats['top_pages'] as $page): ?>
        <tr>
            <td><?= $page['page_url'] ?></td>
            <td><?= $page['views'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>