# Suggested Git Workflow

```text
feature/* -> development -> main -> semantic tag -> GitHub Release -> Packagist
```

Example:

```powershell
git checkout -b development
git push -u origin development

git checkout -b feature/package-foundation
# implement + test
git add .
git commit -m "feat: establish package foundation"
git push -u origin feature/package-foundation
```

Use pull requests and require CI before merging.
