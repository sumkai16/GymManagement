<?php
// Shared Food Tracker view for both member and trainer
// Requires an authenticated session with user_id
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Nutrition.php';

$db = (new Database())->getConnection();
$nutritionModel = new Nutrition($db);
$user_id = $_SESSION['user_id'] ?? 0;
$today = date('Y-m-d');
$totals = $nutritionModel->getDailyTotals($user_id, $today);
$meals = $nutritionModel->getMealsByDate($user_id, $today);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition - Food - FitNexus</title>
    <?php $ms = __DIR__.'/../../assets/css/member_styles.css'; $ns = __DIR__.'/../../assets/css/nutrition_styles.css'; ?>
    <link rel="stylesheet" href="../../assets/css/member_styles.css?v=<?= @filemtime($ms) ?: time() ?>">
    <link rel="stylesheet" href="../../assets/css/nutrition_styles.css?v=<?= @filemtime($ns) ?: time() ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="page-nutrition">
    <?php include __DIR__ . '/../components/dynamic_sidebar.php'; ?>
    <div class="main-content">
        <div class="content-wrapper">
            <div class="nutrition-header">
                <h1>Food Tracker</h1>
                <p>Log meals and see your daily nutrition at a glance.</p>
            </div>
            <div class="nutrition-grid">
                <div class="nutrition-card">
                    <h3><i class='bx bx-plus-circle'></i> Add Meal</h3>
                    <form method="POST" action="../../controllers/NutritionController.php" class="quick-add-form" id="addMealForm">
                        <input type="hidden" name="action" value="add_meal">
                        <input type="hidden" name="food_id" id="food_id">
                        <div class="form-field">
                            <label>Food</label>
                            <input type="text" name="food_item" id="food_name" list="food_suggestions" required placeholder="e.g. rice, cooked">
                            <datalist id="food_suggestions"></datalist>
                        </div>
                        <div class="form-field">
                            <label>Weight (g)</label>
                            <input type="number" name="weight_g" id="weight_g" min="1" step="1" value="100" required>
                        </div>
                        <div id="macroPreview" style="display:none; width:100%; gap:12px; align-items:stretch; margin:8px 0 10px; flex-wrap:nowrap; justify-content:space-between;">
                            <div class="macro-pill" style="flex:1; min-width:0; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; background:#f9fafb; display:flex; align-items:center; justify-content:space-between;">
                                <span style="font-size:.8rem; color:#6b7280;">kcal</span>
                                <span id="prev_cal" style="font-weight:700; font-size:1.05rem; color:#111827;">0</span>
                            </div>
                            <div class="macro-pill" style="flex:1; min-width:0; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; background:#f9fafb; display:flex; align-items:center; justify-content:space-between;">
                                <span style="font-size:.8rem; color:#6b7280;">protein (g)</span>
                                <span id="prev_pro" style="font-weight:700; font-size:1.05rem; color:#111827;">0</span>
                            </div>
                            <div class="macro-pill" style="flex:1; min-width:0; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; background:#f9fafb; display:flex; align-items:center; justify-content:space-between;">
                                <span style="font-size:.8rem; color:#6b7280;">carbs (g)</span>
                                <span id="prev_car" style="font-weight:700; font-size:1.05rem; color:#111827;">0</span>
                            </div>
                            <div class="macro-pill" style="flex:1; min-width:0; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; background:#f9fafb; display:flex; align-items:center; justify-content:space-between;">
                                <span style="font-size:.8rem; color:#6b7280;">fats (g)</span>
                                <span id="prev_fat" style="font-weight:700; font-size:1.05rem; color:#111827;">0</span>
                            </div>
                        </div>
                        <div class="form-field">
                            <label>Date</label>
                            <input type="date" name="date" value="<?= htmlspecialchars($today) ?>">
                        </div>
                        <button type="submit" class="btn-add"><i class='bx bx-plus'></i> Add</button>
                    </form>
                </div>
                <div class="nutrition-card">
                    <h3><i class='bx bx-pie-chart-alt-2'></i> Today's Totals</h3>
                    <div class="totals-grid">
                        <div class="total-card">
                            <i class='bx bx-fire icon'></i>
                            <div class="value"><?= (int)($totals['calories'] ?? 0) ?></div>
                            <div class="label">Calories</div>
                        </div>
                        <div class="total-card">
                            <i class='bx bx-dna icon'></i>
                            <div class="value"><?= (float)($totals['protein'] ?? 0) ?>g</div>
                            <div class="label">Protein</div>
                        </div>
                        <div class="total-card">
                            <i class='bx bx-baguette icon'></i>
                            <div class="value"><?= (float)($totals['carbs'] ?? 0) ?>g</div>
                            <div class="label">Carbs</div>
                        </div>
                        <div class="total-card">
                            <i class='bx bx-droplet icon'></i>
                            <div class="value"><?= (float)($totals['fats'] ?? 0) ?>g</div>
                            <div class="label">Fats</div>
                        </div>
                    </div>
                </div>
                <div class="nutrition-card">
                    <h3><i class='bx bx-bowl-rice'></i> Today's Meals</h3>
                    <div class="meals-list">
                        <?php if (empty($meals)): ?>
                            <div class="empty-state">
                                <i class='bx bx-restaurant'></i>
                                <p>No meals logged today.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($meals as $meal): ?>
                                <div class="meal-row">
                                    <div class="food-name"><?= htmlspecialchars($meal['food_item']) ?></div>
                                    <div class="macro"><span><?= (int)$meal['calories'] ?></span> kcal</div>
                                    <div class="macro"><span><?= (float)$meal['protein'] ?></span> g</div>
                                    <div class="macro"><span><?= (float)$meal['carbs'] ?></span> g</div>
                                    <div class="macro"><span><?= (float)$meal['fats'] ?></span> g</div>
                                    <div class="date"><?= htmlspecialchars($meal['date']) ?></div>
                                    <button type="button" class="btn-delete" onclick="deleteMeal(<?= (int)$meal['nutrition_id'] ?>)"><i class='bx bx-trash'></i> Delete</button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
        $confirmData = [
            'id' => 'delete-meal-confirm',
            'title' => 'Delete Meal',
            'message' => 'Are you sure you want to delete this meal?',
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmButtonClass' => 'danger',
            'show' => true,
        ];
        include __DIR__ . '/../utilities/confirm_modal.php';
    ?>
</body>
</html>

<script>
// Simple autocomplete + preview using controller endpoints
(function(){
  const input = document.getElementById('food_name');
  const list  = document.getElementById('food_suggestions');
  const hiddenId = document.getElementById('food_id');
  const weight = document.getElementById('weight_g');
  const map = new Map(); // name -> id
  let t;

  function setPreview(data){
    const wrap = document.getElementById('macroPreview');
    if (!data || !data.success) { wrap.style.display = 'none'; return; }
    const c = data.computed || {};
    document.getElementById('prev_cal').textContent = c.calories ?? 0;
    document.getElementById('prev_pro').textContent = c.protein ?? 0;
    document.getElementById('prev_car').textContent = c.carbs ?? 0;
    document.getElementById('prev_fat').textContent = c.fats ?? 0;
    wrap.style.display = 'flex';
  }

  function refreshPreview(){
    const w = parseFloat(weight.value || '0');
    const fid = parseInt(hiddenId.value || '0', 10) || 0;
    const name = input.value || '';
    if (w <= 0 || (!fid && name.trim().length < 2)) { setPreview(null); return; }
    const params = new URLSearchParams();
    params.set('action','compute_macros');
    params.set('weight_g', String(w));
    if (fid) params.set('food_id', String(fid)); else params.set('food_name', name);
    fetch('../../controllers/NutritionController.php?' + params.toString())
      .then(r=>r.json())
      .then(setPreview)
      .catch(()=>setPreview(null));
  }

  function search(){
    const q = input.value.trim();
    if (q.length < 2) { list.innerHTML=''; hiddenId.value=''; setPreview(null); return; }
    fetch('../../controllers/NutritionController.php?action=search_food&q=' + encodeURIComponent(q))
      .then(r=>r.json())
      .then(rows=>{
        list.innerHTML = '';
        map.clear();
        (rows||[]).forEach(row=>{
          const opt = document.createElement('option');
          opt.value = row.name;
          list.appendChild(opt);
          map.set(String(row.name), Number(row.food_id));
        });
      })
      .catch(()=>{});
  }

  input.addEventListener('input', ()=>{ hiddenId.value = map.get(input.value) || ''; clearTimeout(t); t = setTimeout(()=>{ search(); refreshPreview(); }, 250); });
  input.addEventListener('change', ()=>{ hiddenId.value = map.get(input.value) || ''; refreshPreview(); });
  weight.addEventListener('input', ()=>{ clearTimeout(t); t = setTimeout(refreshPreview, 200); });
})();

// Delete meal with confirmation modal
(function(){
  let pendingDeleteMealId = null;
  document.addEventListener('DOMContentLoaded', function(){
    const m = document.getElementById('delete-meal-confirm');
    if (m) { m.style.display = 'none'; }
  });
  window.deleteMeal = function(nutritionId){
    pendingDeleteMealId = nutritionId;
    const modalId = 'delete-meal-confirm';
    window.confirmModalActions = window.confirmModalActions || {};
    window.confirmModalActions[modalId] = function(){
      const fd = new FormData();
      fd.append('action','delete_meal');
      fd.append('nutrition_id', pendingDeleteMealId);
      fetch('../../controllers/NutritionController.php', { method: 'POST', body: fd })
        .then(r=>r.json())
        .then(d=>{ if(d && (d.success === true || d.success === 1)) { location.reload(); } else { location.reload(); } })
        .catch(()=>{ location.reload(); })
        .finally(()=>{ pendingDeleteMealId = null; });
    };
    const modal = document.getElementById(modalId);
    if (modal) { modal.style.display = 'flex'; }
  }
})();
</script>
