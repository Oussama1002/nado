# WhatsApp — Modèles / قوالب (autoenchere.ma)

Catégories Meta : **UTILITY** (transactionnel, approbation facile), **MARKETING**
(promotionnel, opt-in requis), **AUTHENTICATION** (OTP uniquement — déjà en place
via `otp_verification`).

Chaque modèle est donné en deux langues : **FR** (`fr`) et **AR** (`ar`). Créez
les deux variantes dans Meta avec le **même nom** et une `language` différente.

Variables `{{1}}`, `{{2}}`… à remplir depuis `admin/whatsapp` au moment de l'envoi.

---

## UTILITY

### 1. `annonce_recue` — annonce de vente reçue
- **FR** : Bonjour {{1}}, nous avons bien reçu votre annonce pour votre {{2}}. Notre équipe la validera sous 24h et vous recontactera au {{3}}. Merci — autoenchere.ma
- **AR** : مرحبا {{1}}، لقد توصلنا بإعلانكم الخاص بـ {{2}}. سيقوم فريقنا بالتحقق منه خلال 24 ساعة والاتصال بكم على الرقم {{3}}. شكرا لثقتكم — autoenchere.ma

### 2. `annonce_publiee` — annonce validée et en ligne
- **FR** : Bonjour {{1}}, votre annonce {{2}} est en ligne sur autoenchere.ma. Lien : {{3}}. Bonnes ventes !
- **AR** : مرحبا {{1}}، تم نشر إعلانكم {{2}} على autoenchere.ma. الرابط: {{3}}. نتمنى لكم بيعا موفقا.

### 3. `demande_contact` — un acheteur a envoyé un message
- **FR** : Bonjour {{1}}, un acheteur est intéressé par votre {{2}}. Son message : "{{3}}". Répondez-lui au {{4}}.
- **AR** : مرحبا {{1}}، هناك مشتر مهتم بـ {{2}}. رسالته: "{{3}}". يمكنكم الرد عليه عبر الرقم {{4}}.

### 4. `rdv_essai` — rendez-vous d'essai confirmé
- **FR** : Bonjour {{1}}, votre essai de la {{2}} est confirmé le {{3}} à {{4}}. Adresse : {{5}}. Pour annuler, répondez à ce message.
- **AR** : مرحبا {{1}}، تم تأكيد موعد تجربة {{2}} يوم {{3}} على الساعة {{4}}. العنوان: {{5}}. للإلغاء، يرجى الرد على هذه الرسالة.

### 5. `reprise_estimation` — estimation de reprise (استبدال)
- **FR** : Bonjour {{1}}, l'estimation de reprise de votre {{2}} est de {{3}} MAD. Offre valable 7 jours. Répondez pour planifier l'inspection.
- **AR** : مرحبا {{1}}، قيمة تقدير استبدال سيارتكم {{2}} هي {{3}} درهم. العرض صالح لمدة 7 أيام. ردوا على هذه الرسالة لتحديد موعد المعاينة.

### 6. `financement_simulation` — simulation de financement
- **FR** : Bonjour {{1}}, votre simulation de financement pour la {{2}} : {{3}} MAD/mois sur {{4}} mois. Un conseiller vous rappellera au {{5}}.
- **AR** : مرحبا {{1}}، محاكاة التمويل الخاصة بـ {{2}}: {{3}} درهم شهريا على مدى {{4}} شهرا. سيتصل بكم أحد المستشارين على الرقم {{5}}.

### 7. `enchere_acces_approuve` — accès Auto-Enchère activé
- **FR** : Bonjour {{1}}, votre accès Auto-Enchère est activé. Connectez-vous : {{2}}. Les prochaines enchères démarrent le {{3}}.
- **AR** : مرحبا {{1}}، تم تفعيل ولوجكم إلى منصة Auto-Enchère. سجلوا الدخول عبر: {{2}}. تنطلق المزادات القادمة يوم {{3}}.

### 8. `enchere_rappel` — enchère qui démarre bientôt
- **FR** : Bonjour {{1}}, l'enchère {{2}} démarre dans 1h ({{3}}). {{4}} véhicules disponibles. Rejoindre : {{5}}
- **AR** : مرحبا {{1}}، ينطلق المزاد {{2}} بعد ساعة ({{3}}). {{4}} سيارة متوفرة. للمشاركة: {{5}}

### 9. `enchere_gagnee` — enchère remportée
- **FR** : Félicitations {{1}} ! Vous avez remporté le {{2}} pour {{3}} MAD. Procédez au paiement sous 48h : {{4}}
- **AR** : مبروك {{1}}! لقد ربحتم مزاد {{2}} بمبلغ {{3}} درهم. يرجى إتمام الأداء خلال 48 ساعة عبر: {{4}}

### 10. `enchere_perdue` — surenchérie
- **FR** : Bonjour {{1}}, vous avez été surenchéri sur {{2}}. Prix actuel : {{3}} MAD. Reprendre l'enchère : {{4}}
- **AR** : مرحبا {{1}}، تم تجاوز عرضكم في المزاد {{2}}. السعر الحالي: {{3}} درهم. للعودة إلى المزاد: {{4}}

### 11. `paiement_recu` — paiement confirmé
- **FR** : Bonjour {{1}}, nous avons bien reçu votre paiement de {{2}} MAD pour la {{3}}. Livraison prévue le {{4}}.
- **AR** : مرحبا {{1}}، لقد توصلنا بأدائكم البالغ {{2}} درهم مقابل {{3}}. من المنتظر تسليم السيارة يوم {{4}}.

### 12. `cartegrise_verifiee` — documents vérifiés
- **FR** : Bonjour {{1}}, les documents de votre {{2}} ont été vérifiés. Votre annonce passe en validation finale.
- **AR** : مرحبا {{1}}، تم التحقق من وثائق سيارتكم {{2}}. سيدخل إعلانكم مرحلة المصادقة النهائية.

### 13. `compte_approuve` — compte client approuvé par l'admin
- **FR** : Bonjour {{1}}, votre compte sur autoenchere.ma est validé. Vous pouvez maintenant vous connecter : {{2}}
- **AR** : مرحبا {{1}}، تم تفعيل حسابكم على autoenchere.ma. يمكنكم الآن تسجيل الدخول عبر: {{2}}

---

## MARKETING (opt-in requis)

### 14. `nouveau_modele` — nouvelle annonce correspondant à une recherche sauvegardée
- **FR** : Bonjour {{1}}, une nouvelle {{2}} à {{3}} MAD vient d'être publiée sur autoenchere.ma. La voir : {{4}}
- **AR** : مرحبا {{1}}، تم نشر {{2}} جديدة بسعر {{3}} درهم على autoenchere.ma. للاطلاع: {{4}}

### 15. `baisse_prix` — baisse de prix sur un favori
- **FR** : Bonjour {{1}}, la {{2}} que vous suiviez est passée de {{3}} à {{4}} MAD. Voir : {{5}}
- **AR** : مرحبا {{1}}، انخفض سعر {{2}} التي تتابعونها من {{3}} إلى {{4}} درهم. للاطلاع: {{5}}

### 16. `enchere_decouverte` — invitation à une enchère à venir
- **FR** : Bonjour {{1}}, enchère {{2}} le {{3}} : {{4}} véhicules à partir de {{5}} MAD. S'inscrire : {{6}}
- **AR** : مرحبا {{1}}، مزاد {{2}} يوم {{3}}: {{4}} سيارة انطلاقا من {{5}} درهم. للتسجيل: {{6}}

### 17. `relance_vente` — annonce qui reçoit peu de vues
- **FR** : Bonjour {{1}}, votre annonce {{2}} a reçu {{3}} vues. Ajoutez des photos ou baissez le prix pour attirer plus d'acheteurs : {{4}}
- **AR** : مرحبا {{1}}، توصل إعلانكم {{2}} بـ {{3}} مشاهدة. أضيفوا صورا أو خفضوا السعر لجذب المزيد من المشترين: {{4}}

---

## Règles Meta à respecter avant soumission

- Chaque variable doit être **entourée de texte** — un message qui commence ou se termine par `{{1}}` est rejeté.
- Pas d'URL raccourcies (bit.ly, etc.). Utilisez `https://autoenchere.ma/...` entier, ou un **bouton URL** avec variable de chemin.
- MARKETING : opt-in explicite obligatoire (case à cocher "je veux recevoir des alertes WhatsApp" + conservation de la preuve).
- Pas d'emojis dans le `name` du template — uniquement `[a-z0-9_]`, minuscules.
- Soumettez les deux variantes **FR et AR sous le même `name`** (Meta indexe par `name`+`language`).

Créez-les depuis **`/admin/whatsapp/templates/create`** ou directement dans Meta Business Manager. L'approbation prend de quelques minutes à quelques heures.
